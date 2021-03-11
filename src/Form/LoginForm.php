<?php
namespace App\Form;

use App\Form\AbstractForm;
use Config\Request\Request;
use Config\Request\Parameter;
use App\Service\Validation\LoginValidation;

class LoginForm extends AbstractForm
{
    public string $email;
    public string $password;
    public bool $rememberme;

    private $validation;

    public function __construct(LoginValidation $validation)
    {
        $this->validation = $validation;
    }
    
    public function handleRequest(Request $request)
    {
        if($request->getMethod() == 'POST') {
            $this->hydrateForm($request->request);
            $this->validation->validate($this);
        }
    }

    public function hydrateForm(Parameter $post)
    {
        $this->email = $post->get('email') ?: '';
        $this->password = $post->get('password') ?: '';
        $this->rememberme = $post->get('rememberme') ?: false;
        $this->csrfToken = $post->get('csrf_token') ?: '';
        $this->isSubmitted = true;
    }
}
