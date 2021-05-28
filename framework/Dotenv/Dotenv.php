<?php

namespace Framework\Dotenv;

class Dotenv
{
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
        }

        // ensures that APP_DEBUG is defined
        $_SERVER['APP_DEBUG'] = $_ENV['APP_DEBUG'] = $_SERVER['APP_DEBUG'] === true ?? false;
    }
}
