<?php

namespace Framework\Dotenv;

class Dotenv
{
    /**
     * Environment variables declared in .env.local (default .env).
     */
    private array $variables = [];

    /**
     * loads environment variables from env file
     */
    public function loadEnv(string $path): void
    {
        $env = (string) file_get_contents($path);
        $data = (array) json_decode($env);

        foreach ($data as $key => $value) {
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
            $this->set($key, $value);
        }

        // ensures that APP_DEBUG is defined
        $key = 'APP_DEBUG';
        $_SERVER[$key] = $_ENV[$key] = $_SERVER[$key] === true ?? false;
        $this->set($key, $_SERVER[$key]);
    }

    public function all(): array
    {
        return $this->variables;
    }

    public function keys(): array
    {
        return array_keys($this->variables);
    }

    public function replace(array $parameters = []): void
    {
        $this->variables = $parameters;
    }

    public function add(array $parameters = []): void
    {
        $this->variables = array_replace($this->variables, $parameters);
    }

    /**
     * @return mixed
     */
    public function get(string $key, ?string $default = null)
    {
        return array_key_exists($key, $this->variables) ? $this->variables[$key] : $default;
    }

    /**
     * @param string|array|object|bool $value
     */
    public function set(string $key, $value): void
    {
        $this->variables[$key] = $value;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->variables);
    }

    public function remove(string $key): void
    {
        unset($this->variables[$key]);
    }
}
