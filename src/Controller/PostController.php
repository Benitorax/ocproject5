<?php

namespace App\Controller;

use App\DAO\PostDAO;
use Framework\Response\Response;
use App\Service\Pagination\Paginator;
use Framework\Controller\AbstractController;

class PostController extends AbstractController
{
    /**
     * Displays a list of posts.
     */
    public function index(): Response
    {
        // retrieves the page number and search terms of the query string
        $pageNumber = (int) $this->request->query->get('page') ?: 1;
        $searchTerms = $this->request->query->get('q');

        // sets the query for the pagination
        /** @var PostDAO */
        $postDAO = $this->get(PostDAO::class);
        $postDAO->setIsPublishedAndSearchQuery($searchTerms);

        // creates the pagination for the template
        /** @var Paginator */
        $paginator = $this->get(Paginator::class);

        $pagination = $paginator->paginate(
            $postDAO,
            $pageNumber,
            5
        );

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
        /** @var PostDAO */
        $postDAO = $this->get(PostDAO::class);

        $post = $postDAO->getOneBySlug($slug);

        return $this->render('post/show.html.twig', [
            'post' => $post
        ]);
    }
}
