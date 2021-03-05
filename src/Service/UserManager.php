<?php
namespace App\Service;

use App\Model\User;
use App\DAO\UserDAO;
use App\Form\LoginForm;
use App\Form\RegisterForm;
use Config\Request\Parameter;

class UserManager
{
    private $userDAO;
    private $encoder;
    
    public function __construct(UserDAO $userDAO, PasswordEncoder $encoder)
    {
        $this->userDAO = $userDAO;
        $this->encoder = $encoder;
    }

    public function hydrateLoginForm(LoginForm $form, Parameter $post): LoginForm
    {
        $form->email = $post->get('email') ?: '';
        $form->password = $post->get('password') ?: '';
        $form->rememberme = $post->get('rememberme') ?: false;

        return $form;
    }
    
    public function hydrateRegisterForm(RegisterForm $form, Parameter $post): RegisterForm
    {
        $form->email = $post->get('email') ?: '';
        $form->password1 = $post->get('password1') ?: '';
        $form->password2 = $post->get('password2') ?: '';
        $form->username = $post->get('username') ?: '';
        $form->terms = $post->get('terms') ?: false;

        return $form;
    }

    public function saveNewUser(RegisterForm $form): User
    {
        $user = new User();

        $user->setId(IdGenerator::generate())
        ->setEmail($form->email)
        ->setPassword($this->encoder->encode($form->password1))
        ->setUsername($form->username)
        ->setCreatedAt(new \DateTime())
        ->setUpdatedAt(new \DateTime());

        $this->userDAO->add($user);

        return $user;
    }
}
