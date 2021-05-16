<?php

namespace App\Controller\Admin;

use App\Service\CommentManager;
use App\Service\EntityDeleter;
use Framework\Response\Response;
use Framework\Controller\AbstractController;

class CommentController extends AbstractController
{
    private CommentManager $commentManager;
    private EntityDeleter $entityDeleter;

    public function __construct(CommentManager $commentManager, EntityDeleter $entityDeleter)
    {
        $this->commentManager = $commentManager;
        $this->entityDeleter = $entityDeleter;
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
            $this->entityDeleter->deleteCommentByUuid($uuid);
            $this->addFlash('success', 'The comment has been deleted with success!');
        }

        return $this->redirectToUrl($this->request->server->get('HTTP_REFERER'));
    }
}
