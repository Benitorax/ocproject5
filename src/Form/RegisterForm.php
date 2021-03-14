<?php
namespace App\Form;

use Config\Request\Request;
use Config\Request\Parameter;
use App\Service\Validation\RegisterValidation;

class RegisterForm extends AbstractForm
{
    public string $email;
    public string $password1;
    public string $password2;
    public string $username;
    public bool $terms;

    private RegisterValidation $validation;

    public function __construct(RegisterValidation $validation)
    {
        $this->validation = $validation;
    }

    public function handleRequest(Request $request): void
    {
        if ($request->getMethod() == 'POST') {
            $this->hydrate($request->request);
            $this->validation->validate($this);
        }
    }

    public function hydrate(Parameter $post): void
    {
        $this->email = $post->get('email') ?: '';
        $this->password1 = $post->get('password1') ?: '';
        $this->password2 = $post->get('password2') ?: '';
        $this->username = $post->get('username') ?: '';
        $this->terms = $post->get('terms') ?: false;
        $this->csrfToken = $post->get('csrf_token') ?: '';
        $this->isSubmitted = true;
    }
}
