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
}
