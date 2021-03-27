<?php

namespace App\Controller\Admin;

use App\DAO\PostDAO;
use App\Form\PostForm;
use App\Service\PostManager;
use Framework\Response\Response;
use Framework\Controller\AbstractController;

class AdminPostController extends AbstractController
{
    private PostManager $postManager;
    private PostDAO $postDAO;

    public function __construct(PostManager $postManager, PostDAO $postDAO)
    {
        $this->postManager = $postManager;
        $this->postDAO = $postDAO;
    }

    /**
     * Displays a list of posts.
     */
    public function index(): Response
    {
        $this->denyAccessUnlessGranted(['admin']);

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
        $this->denyAccessUnlessGranted(['admin']);

        $form = $this->createForm(PostForm::class);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            $this->postManager->manageCreatePost($post);
            $this->addFlash('success', 'The post has been created with success!');

            return $this->redirectToRoute('admin_post_index');
        }

        return $this->render('admin/post/create.html.twig', ['form' => $form]);
    }

    /**
     * Displays a form page to create a post.
     */
    public function edit(string $id): Response
    {
        $this->denyAccessUnlessGranted(['admin']);

        $post = $this->postDAO->getOneById($id);
        $form = $this->createForm(PostForm::class, $post);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            $this->postManager->manageEditPost($post);
            $this->addFlash('success', 'The post has been saved with success!');

            return $this->redirectToRoute('admin_post_index');
        }

        return $this->render('admin/post/edit.html.twig', ['form' => $form]);
    }

    /**
     * Deletes a post.
     */
    public function delete(string $id): Response
    {
        $this->denyAccessUnlessGranted(['admin']);

        if ($this->isCsrfTokenValid()) {
            $this->postDAO->deleteById($id);
            $this->addFlash('success', 'The post has been deleted with success!');
        }

        return $this->redirectToRoute('admin_post_index');
    }
}
