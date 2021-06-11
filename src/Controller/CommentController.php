<?php

namespace App\Controller;

use App\Model\Post;
use App\Form\CommentForm;
use App\Model\User;
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
        // checks if post exists
        $post = $this->postManager->getPostByUuid($uuid);
        if (!$post instanceof Post || ($post instanceof Post && !$post->getIsPublished())) {
            return $this->json(['error' => 'Post does not exist.'], 404);
        }

        // checks if users exists and is not blocked
        $user = $this->getUser();
        if (null === $user || ($user instanceof User && $user->getIsBlocked())) {
            return $this->json(['error' => 'You are not allowed to submit comment.'], 403);
        }

        // handles form
        $form = $this->createForm(CommentForm::class);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
            $this->commentManager->manageNewComment($comment, $post);
            $this->addFlash('success', 'The comment has been submitted with success!');

            // returns a success JSON response
            return $this->json([
                'url' => $this->urlGenerator->generate('post_show', [
                    'slug' => $post->getSlug()
                ])
            ], 303);
        }

        // returns a JSON response with errors
        return $this->json([
            'error' => $form->getErrors(),
            // sends a new token if the previous token is invalid
            'csrf_token' => null === $form->getErrors()['csrf'] ? null : $this->tokenManager->generateToken()
        ], 422);
    }
}
