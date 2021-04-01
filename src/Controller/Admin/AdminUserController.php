<?php

namespace App\Controller\Admin;

use App\Service\UserManager;
use Framework\Response\Response;
use Framework\Controller\AbstractController;

class AdminUserController extends AbstractController
{
    private UserManager $userManager;

    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
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
}
