<?php

namespace App\Controller;

use App\Model\Post;
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
        $pageNumber = (int) $this->request->query->get('page') ?: 1;

        $paginator = $this->get(Paginator::class);
        $pagination = $paginator->paginate(
            $pageNumber, // page number
            Post::class, // class to query
            ['p_isPublished' => true] // parameters
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
        $post = $this->get(PostDAO::class)->getOneBy(['slug' => $slug]);

        return $this->render('post/show.html.twig', [
            'post' => $post
        ]);
    }
}
