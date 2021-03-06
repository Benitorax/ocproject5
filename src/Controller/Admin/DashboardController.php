<?php

namespace App\Controller\Admin;

use App\Service\CommentManager;
use App\Service\PostManager;
use Framework\Response\Response;
use Framework\Controller\AbstractController;

class DashboardController extends AbstractController
{
    private PostManager $postManager;
    private CommentManager $commentManager;

    public function __construct(PostManager $postManager, CommentManager $commentManager)
    {
        $this->postManager = $postManager;
        $this->commentManager = $commentManager;
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

        return $this->redirectToRoute('admin_dashboard_comment');
    }

    /**
     * Displays the dashboard page with drafts.
     */
    public function showDraftPosts(): Response
    {
        $this->denyAccessUnlessGranted(['admin']);

        $pageNumber = (int) $this->request->query->get('page');
        $pagination = $this->postManager->getPaginationForDraftPosts($pageNumber);

        return $this->render('admin/dashboard/post_index.html.twig', [
            'pagination' => $pagination,
            'queryString' => http_build_query($this->request->query->all())
        ]);
    }

    /**
     * Displays the dashboard page with comments which need to be validated or invalidated.
     */
    public function showComments(): Response
    {
        $this->denyAccessUnlessGranted(['admin']);

        $pageNumber = (int) $this->request->query->get('page');
        $pagination = $this->commentManager->getPaginationForCommentsToValidate($pageNumber);

        return $this->render('admin/dashboard/comment_index.html.twig', [
            'pagination' => $pagination,
            'queryString' => http_build_query($this->request->query->all())
        ]);
    }
}
