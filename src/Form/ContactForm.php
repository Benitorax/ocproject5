<?php
namespace App\Form;

use DateTime;
use App\Model\User;
use App\Form\AbstractForm;
use Config\Request\Request;
use Config\Session\Session;
use Config\Request\Parameter;
use App\Service\Validation\ContactValidation;

class ContactForm extends AbstractForm
{
    public User $user;
    public string $subject;
    public string $content;
    public DateTime $createdAt;

    private ContactValidation $validation;

    public function __construct(ContactValidation $validation)
    {
        $this->validation = $validation;
    }
    
    public function handleRequest(Request $request): void
    {
        if ($request->getMethod() == 'POST') {
            /** @var Session */
            $session = $request->getSession();
            $this->user = $session->get('user');
            $this->hydrate($request->request);
            $this->validation->validate($this);
        }
    }

    public function hydrate(Parameter $post): void
    {
        $this->subject = $post->get('subject') ?: '';
        $this->content = trim($post->get('content')) ?: '';
        $this->csrfToken = $post->get('csrf_token') ?: '';
        $this->isSubmitted = true;
    }

    public function clear(): void
    {
        $this->subject = '';
        $this->content = '';
        $this->errors = [];
        $this->csrfToken = '';
        $this->isSubmitted = false;
        $this->isValid =  false;
    }
}
