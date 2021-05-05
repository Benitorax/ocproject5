<?php

namespace App\Controller;

use App\Model\Post;
use App\Form\CommentForm;
use App\Service\PostManager;
use App\Service\CommentManager;
use Framework\Response\Response;
use Framework\Router\UrlGenerator;
use Framework\Controller\AbstractController;
use Framework\Security\Csrf\CsrfTokenManager;

class CommentController extends AbstractController
{
    private PostManager $postManager;
    private CommentManager $commentManager;
    private CsrfTokenManager $tokenManager;
    private UrlGenerator $urlGenerator;


    public function __construct(
        PostManager $postManager,
        CsrfTokenManager $tokenManager,
        CommentManager $commentManager,
        UrlGenerator $urlGenerator
    ) {
        $this->postManager = $postManager;
        $this->commentManager = $commentManager;
        $this->tokenManager = $tokenManager;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * Creates and saves a new comment.
     */
    public function create(string $uuid): Response
    {
        $post = $this->postManager->getPostByUuid($uuid);
        if (!$post instanceof Post) {
            return $this->json(['error' => 'Post doesn\'t exist.'], 404);
        }

        $form = $this->createForm(CommentForm::class);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
            $this->commentManager->manageNewComment($comment, $post);
            $this->addFlash('success', 'The comment has been submitted with success!');

            return $this->json([
                'url' => $this->urlGenerator->generate('post_show', [
                    'slug' => $post->getSlug()
                ])
            ], 303);
        }

        return $this->json([
            'error' => $form->getErrors(),
            'csrf_token' => null === $form->getErrors()['csrf'] ? null : $this->tokenManager->generateToken()
        ], 422);
    }
}
