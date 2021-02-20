<?php
namespace App\Controller;

use App\Model\Post;
use App\Controller\Controller;

class AppController extends Controller
{
    public function home()
    {
        // if(isset($post['submit'])) {
        //     $articleDAO = new ArticleDAO();
        //     $articleDAO->addArticle($post);
        //     header('Location: ../public/index.php');
        // }
        $post = new Post();
        $post->setId('123')
        ->setTitle('Mon titre de la mort')
        ->setSlug('mon-titre-de-la-mort')
        ->setShortText('Mon introduction')
        ->setText('Le texte complètement vide')
        ->setCreatedAt(new \DateTime())
        ->setUpdatedAt(new \DateTime())
        ->setIsPublished(true)
        ->setUserId('456');
        
        $this->postDAO->addPost($post);

        return $this->view->render('home/home', [
            'post' => $post
        ]);
    }
}