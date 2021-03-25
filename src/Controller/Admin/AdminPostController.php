<?php

namespace App\Controller\Admin;

use App\Form\PostCreateForm;
use App\Service\PostManager;
use Framework\Response\Response;
use Framework\Container\Container;
use Framework\Controller\AbstractController;

class AdminPostController extends AbstractController
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
        $pagination = $this->postManager->getPaginationForAllPosts($searchTerms, $pageNumber);

        return $this->render('admin/post/index.html.twig', [
            'pagination' => $pagination,
            'searchTerms' => $searchTerms,
            'searchQueryString' => http_build_query(['q' => $searchTerms])
        ]);
    }

    /**
     * Displays a form page to create a post.
     */
    public function create(): Response
    {
        /** @var PostCreateForm */
        $form = $this->createForm(PostCreateForm::class);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->postManager->managePostCreate($form);
            $this->addFlash('success', 'You have created a post with success!');

            return $this->redirectToRoute('admin_post_index');
        }

        return $this->render('admin/post/create.html.twig', ['form' => $form]);
    }
}
