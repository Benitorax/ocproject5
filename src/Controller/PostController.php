<?php

namespace App\Controller;

use App\DAO\PostDAO;
use Framework\Response\Response;
use Framework\Controller\AbstractController;

class PostController extends AbstractController
{
    /**
     * Displays a list of posts.
     */
    public function index(): Response
    {
        $posts = $this->get(PostDAO::class)->getAll();

        return $this->render('post/index.html.twig', [
            'posts' => $posts
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
