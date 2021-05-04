<?php

namespace App\Controller;

use App\Form\CommentForm;
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
        if (null === $post = $this->postManager->getOneBySlug($slug)) {
            throw new Exception('Post doesn\'t exist.', 404);
        }

        $form = $this->createForm(CommentForm::class);
        $form->handleRequest($this->request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $comment = $form->getData();
                $this->get(CommentManager::class)->manageNewComment($comment, $post);
                $form->clear();
                $this->addFlash('success', 'The comment has been submitted with success!');

                // retrieves Post with updated Comments
                $post = $this->postManager->getOneBySlug($slug);
            } else {
                $this->addFlash('danger', 'The comment was not submitted, please check error in the form.');
            }
        }

        return $this->render('post/show.html.twig', [
            'post' => $post,
            'form' => $form
        ]);
    }
}
