<?php

namespace App\Controller;

use Framework\Response\Response;
use Framework\Container\Container;
use App\Service\PostManager;
use Framework\Controller\AbstractController;

class PostController extends AbstractController
{
    private PostManager $postManager;

    public function __construct(
        Container $container,
        PostManager $postManager
    ) {
        parent::__construct($container);
        $this->postManager = $postManager;
    }

    /**
     * Displays a list of posts.
     */
    public function index(): Response
    {
        // retrieves the page number and search terms of the query string
        $pageNumber = (int) $this->request->query->get('page') ?: 1;
        $searchTerms = $this->request->query->get('q');

        // get the pagination
        $pagination = $this->postManager->getPaginationForIsPublishedAndSearchTerms($searchTerms, $pageNumber);

        return $this->render('post/index.html.twig', [
            'pagination' => $pagination,
            'searchTerms' => $searchTerms,
            'searchQueryString' => http_build_query(['q' => $searchTerms])
        ]);
    }

    /**
     * Displays a single post.
     */
    public function show(string $slug): Response
    {
        return $this->render('post/show.html.twig', [
            'post' => $this->postManager->getOneBySlug($slug),
        ]);
    }
}
