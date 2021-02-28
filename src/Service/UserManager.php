<?php
namespace App\Service;

use App\Model\User;
use App\DAO\UserDAO;
use App\Model\UserDTO;
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

    public function hydrateUserDTO(UserDTO $userDTO, Parameter $post)
    {
        $userDTO->email = $post->get('email') ?: '';
        $userDTO->password1 = $post->get('password1') ?: '';
        $userDTO->password2 = $post->get('password2') ?: '';
        $userDTO->username = $post->get('username') ?: '';
        $userDTO->terms = $post->get('terms') ?: false;

        return $userDTO;
    }

    public function saveNewUser(UserDTO $userDTO)
    {
        $user = new User();

        $user->setId(IdGenerator::generate())
        ->setEmail($userDTO->email)
        ->setPassword($this->encoder->encode($userDTO->password1))
        ->setUsername($userDTO->username)
        ->setCreatedAt(new \DateTime())
        ->setUpdatedAt(new \DateTime());

        $this->userDAO->add($user);

        return $user;
    }
}