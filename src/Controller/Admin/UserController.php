<?php

namespace App\Controller\Admin;

use App\Model\User;
use App\Service\UserManager;
use App\Service\EntityDeleter;
use Framework\Response\Response;
use Framework\Controller\AbstractController;

class UserController extends AbstractController
{
    private UserManager $userManager;
    private EntityDeleter $entityDeleter;

    public function __construct(UserManager $userManager, EntityDeleter $entityDeleter)
    {
        $this->userManager = $userManager;
        $this->entityDeleter = $entityDeleter;
    }

    /**
     * Displays a list of all the users.
     */
    public function index(): Response
    {
        $this->denyAccessUnlessGranted(['admin']);

        // retrieves data from the query string
        $pageNumber = $this->request->query->get('page');
        $searchTerms = $this->request->query->get('q');
        $filter = $this->request->query->get('filter');

        return $this->render('admin/user/index.html.twig', [
            'pagination' => $this->userManager->getPaginationForAllUsers($filter, $searchTerms, $pageNumber),
            'searchTerms' => $searchTerms,
            'queryString' => http_build_query($this->request->query->all())
        ]);
    }

    /**
     * Blocks a user.
     */
    public function block(string $uuid): Response
    {
        $this->denyAccessUnlessGranted(['admin']);

        if ($this->isCsrfTokenValid()) {
            $this->userManager->blockUserByUuid($uuid);
            $this->addFlash('success', 'The user has been blocked with success!');
        }

        return $this->redirectToUrl($this->request->server->get('HTTP_REFERER'));
    }

    /**
     * Unblocks a user.
     */
    public function unblock(string $uuid): Response
    {
        $this->denyAccessUnlessGranted(['admin']);

        if ($this->isCsrfTokenValid()) {
            $this->userManager->unblockUserByUuid($uuid);
            $this->addFlash('success', 'The user has been unblocked with success!');
        }

        return $this->redirectToUrl($this->request->server->get('HTTP_REFERER'));
    }

    /**
     * Deletes a user.
     */
    public function delete(string $uuid): Response
    {
        $this->denyAccessUnlessGranted(['admin']);

        if ($this->isCsrfTokenValid()) {
            /** @var User */
            $user = $this->getUser();

            if ($uuid === $user->getUuid()->toString()) {
                $this->addFlash('danger', 'You can\'t delete your own account!');
            } else {
                $this->entityDeleter->deleteUserByUuid($uuid);
                $this->addFlash('success', 'The user has been deleted with success!');
            }
        }

        return $this->redirectToUrl($this->request->server->get('HTTP_REFERER'));
    }
}
