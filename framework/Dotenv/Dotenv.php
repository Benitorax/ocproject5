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
        $data = is_file($path) ? file_get_contents($path) : null;
        $data = str_replace(["\r\n", "\r"], "\n", (string) $data);
        $data = explode("\n", $data);

        foreach ($data as $row) {
            if (0 === strlen($row) || '#' === $row[0]) {
                continue;
            }

            [$key, $value] = explode('=', $row, 2);
            $value = trim($value);

            // if conditions are true, then sets value to empty string
            // or converts to bool variable
            if (in_array($value, ['""', '\'\''])) {
                $value = '';
            } elseif (in_array($value, ['false', 'true'])) {
                $value = 'true' === $value ? true : false;
            }

            $this->set(trim($key), $value);
        }

        // ensures that APP_DEBUG is defined
        $key = 'APP_DEBUG';
        $_SERVER[$key] = $_ENV[$key] = $this->get($key) === true ?? false;
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
     * @param null|string|bool $default
     * @return mixed
     */
    public function get(string $key, $default = null)
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
