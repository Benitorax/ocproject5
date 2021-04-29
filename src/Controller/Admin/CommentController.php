<?php

namespace App\Controller\Admin;

use App\Service\CommentManager;
use Framework\Response\Response;
use Framework\Controller\AbstractController;

class CommentController extends AbstractController
{
    private CommentManager $commentManager;

    public function __construct(CommentManager $commentManager)
    {
        $this->commentManager = $commentManager;
    }

    /**
     * Validates a comment.
     */
    public function validate(string $uuid): Response
    {
        $this->denyAccessUnlessGranted(['admin']);

        if ($this->isCsrfTokenValid()) {
            $this->commentManager->validateCommentByUuid($uuid);
            $this->addFlash('success', 'The comment has been validated with success!');
        }

        return $this->redirectToUrl($this->request->server->get('HTTP_REFERER'));
    }

    /**
     * Delete a comment.
     */
    public function delete(string $uuid): Response
    {
        $this->denyAccessUnlessGranted(['admin']);

        if ($this->isCsrfTokenValid()) {
            $this->commentManager->deleteCommentByUuid($uuid);
            $this->addFlash('success', 'The comment has been deleted with success!');
        }

        return $this->redirectToUrl($this->request->server->get('HTTP_REFERER'));
    }
}
