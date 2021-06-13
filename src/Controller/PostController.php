<?php

namespace App\Controller;

use App\Form\CommentForm;
use App\Model\Post;
use App\Service\PostManager;
use App\Service\CommentManager;
use Exception;
use Framework\Response\Response;
use Framework\Controller\AbstractController;

class PostController extends AbstractController
{
    private PostManager $postManager;

    public function __construct(PostManager $postManager)
    {
        $this->postManager = $postManager;
    }

    /**
     * Displays a list of published posts.
     */
    public function index(): Response
    {
        // retrieves the page number and search terms of the query string
        $pageNumber = (int) $this->request->query->get('page');
        $searchTerms = $this->request->query->get('q');

        return $this->render('post/index.html.twig', [
            'pagination' => $this->postManager->getPaginationForIsPublishedAndSearchTerms($searchTerms, $pageNumber),
            'searchTerms' => $searchTerms,
            'queryString' => http_build_query($this->request->query->all())
        ]);
    }

    /**
     * Displays a single post and eventually creates a Comment.
     */
    public function show(string $slug): Response
    {
        $post = $this->postManager->getOneBySlug($slug);
        if (null === $post || ($post instanceof Post && !$post->getIsPublished())) {
            throw new Exception('Post doesn\'t exist.', 404);
        }

        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }
}
