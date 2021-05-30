<?php

namespace Framework\Form;

use Framework\Request\Request;

abstract class AbstractForm implements FormInterface
{
    private string $csrfToken = '';
    protected array $errors = [];
    protected bool $isValid = false;
    private bool $isSubmitted = false;

    /**
     * If request method is POST, then hydrate the form and validate it.
     */
    public function handleRequest(Request $request): void
    {
        if ('POST' === $request->getMethod()) {
            //reinitiate the errors and validity before validation
            $this->errors = [];
            $this->isValid = true;

            // hydrates after reinitializing to allow setting errors or change validity
            // inside your custom setter methods.
            $this->hydrate($this, $request);
            $this->isSubmitted = true;
            $this->getValidation()->validate($this);

            foreach ($this->getErrors() as $error) {
                if (!empty($error)) {
                    $this->isValid = false;
                    break;
                }
            }
        }
    }

    /**
     * Hydrates the form with the request.
     */
    public function hydrate(AbstractForm $form, Request $request): void
    {
        foreach ($request->request as $name => $value) {
            $name = $this->snakeCaseToCamelCase($name);
            $method = 'set' . ucfirst($name);

            if (method_exists($form, $method)) {
                $form->$method(trim($value));
            }
        }
    }

    /**
     * Converts the string from snake_case to camelCase for the setter.
     */
    public function snakeCaseToCamelCase(string $string): string
    {
        $pascalCase = implode('', array_map(function ($element) {
            return ucfirst($element);
        }, explode('_', $string)));

        return lcfirst($pascalCase);
    }

    public function getCsrfToken(): string
    {
        return $this->csrfToken;
    }

    public function setCsrfToken(string $csrfToken): self
    {
        $this->csrfToken = $csrfToken;

        return $this;
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }

    public function isSubmitted(): bool
    {
        return $this->isSubmitted;
    }

    public function addError(string $propertyName, ?string $errorMessage = null): void
    {
        $this->errors[$propertyName] = $errorMessage;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
