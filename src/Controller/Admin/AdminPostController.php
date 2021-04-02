<?php

namespace App\Controller\Admin;

use App\Form\PostForm;
use App\Service\PostManager;
use Framework\Response\Response;
use Framework\Controller\AbstractController;

class AdminPostController extends AbstractController
{
    private PostManager $postManager;

    public function __construct(PostManager $postManager)
    {
        $this->postManager = $postManager;
    }

    /**
     * Displays a list of all the posts.
     */
    public function index(): Response
    {
        $this->denyAccessUnlessGranted(['admin']);

        // retrieves the page number and search terms of the query string
        $pageNumber = (int) $this->request->query->get('page');
        $searchTerms = $this->request->query->get('q');

        return $this->render('admin/post/index.html.twig', [
            'pagination' => $this->postManager->getPaginationForAllPosts($searchTerms, $pageNumber),
            'searchTerms' => $searchTerms,
            'queryString' => http_build_query($this->request->query->all())
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
    public function edit(string $uuid): Response
    {
        $this->denyAccessUnlessGranted(['admin']);

        $post = $this->postManager->getPostByUuid($uuid);
        $form = $this->createForm(PostForm::class, $post);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            $this->postManager->manageEditPost($post);
            $this->addFlash('success', 'The post has been saved with success!');

            return $this->redirectToRoute('admin_post_index');
        }

        return $this->render('admin/post/edit.html.twig', [
            'form' => $form,
            'post' => $post
        ]);
    }

    /**
     * Deletes a post.
     */
    public function delete(string $uuid): Response
    {
        $this->denyAccessUnlessGranted(['admin']);

        if ($this->isCsrfTokenValid()) {
            $this->postManager->deletePostByUuid($uuid);
            $this->addFlash('success', 'The post has been deleted with success!');
        }

        return $this->redirectToRoute('admin_post_index');
    }
}
