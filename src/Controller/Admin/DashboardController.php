<?php

namespace App\Controller\Admin;

use App\Service\PostManager;
use Framework\Response\Response;
use Framework\Controller\AbstractController;

class DashboardController extends AbstractController
{
    private PostManager $postManager;

    public function __construct(PostManager $postManager)
    {
        $this->postManager = $postManager;
    }

    /**
     * Redirects to draft page.
     */
    public function index(): Response
    {
        if (false === $this->isGranted(['user'])) {
            return $this->redirectToRoute('login');
        }

        $this->denyAccessUnlessGranted(['admin']);

        return $this->redirectToRoute('admin_dashboard_post_draft');
    }

    /**
     * Displays the dashboard page with drafts.
     */
    public function showDraftPosts(): Response
    {
        $this->denyAccessUnlessGranted(['admin']);

        $pageNumber = (int) $this->request->query->get('page');
        $pagination = $this->postManager->getPaginationForDraftPosts($pageNumber);

        return $this->render('admin/dashboard/index.html.twig', [
            'pagination' => $pagination,
            'queryString' => http_build_query($this->request->query->all())
        ]);
    }
}
