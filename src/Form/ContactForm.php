<?php
namespace App\Form;

use App\Form\AbstractForm;
use Config\Request\Request;
use Config\Request\Parameter;
use App\Service\Validation\ContactValidation;

class ContactForm extends AbstractForm
{
    public string $subject;
    public string $content;

    private ContactValidation $validation;

    public function __construct(ContactValidation $validation)
    {
        $this->validation = $validation;
    }
    
    public function handleRequest(Request $request): void
    {
        if ($request->getMethod() == 'POST') {
            $this->hydrateForm($request->request);
            $this->validation->validate($this);
        }
    }

    public function hydrateForm(Parameter $post): void
    {
        $this->subject = $post->get('subject') ?: '';
        $this->content = $post->get('content') ?: '';
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
