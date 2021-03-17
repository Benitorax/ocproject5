<?php

namespace App\Controller;

use App\DAO\PostDAO;
use Framework\Response\Response;
use Framework\Controller\AbstractController;

class PostController extends AbstractController
{
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
