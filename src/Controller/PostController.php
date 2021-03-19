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
        // retrieves the page number of the query string
        $pageNumber = (int) $this->request->query->get('page') ?: 1;

        /** @var PostDAO */
        $postDAO = $this->get(PostDAO::class);

        // creates a pagination for the template
        /** @var Paginator */
        $paginator = $this->get(Paginator::class);

        $pagination = $paginator->paginate(
            $pageNumber,
            $postDAO,
            ['is_published' => true]
        );

        return $this->render('post/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * Displays a single post.
     */
    public function show(string $slug): Response
    {
        /** @var PostDAO */
        $postDAO = $this->get(PostDAO::class);

        $post = $postDAO->getOneBy(['slug' => $slug]);

        return $this->render('post/show.html.twig', [
            'post' => $post
        ]);
    }
}
