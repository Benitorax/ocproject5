<?php

namespace App\Controller\Admin;

use Framework\View\View;
use App\Service\PostManager;
use Framework\Container\Container;
use Framework\Controller\AbstractController;
use Framework\Response\Response;

class AdminPostController extends AbstractController
{
    private PostManager $postManager;

    public function __construct(
        View $view,
        Container $container,
        PostManager $postManager
    ) {
        parent::__construct($view, $container);
        $this->postManager = $postManager;
    }

    public function index(): Response
    {
        // retrieves the page number and search terms of the query string
        $pageNumber = (int) $this->request->query->get('page') ?: 1;
        $searchTerms = $this->request->query->get('q');

        // get the pagination
        $pagination = $this->postManager->getPaginationForAllPosts($searchTerms, $pageNumber);

        return $this->render('admin/post/index.html.twig', [
            'pagination' => $pagination,
            'searchTerms' => $searchTerms,
            'searchQueryString' => http_build_query(['q' => $searchTerms])
        ]);
    }
}
