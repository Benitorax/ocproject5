<?php

require dirname(__DIR__) . '/vendor/autoload.php';
// Comment or delete the line below for production
require_once dirname(__DIR__) . '/framework/Debug/Debug.php';

use Framework\App;
use Framework\Dotenv\Dotenv;
use Framework\Request\Request;

$dotenv = new Dotenv();
if (file_exists(dirname(__DIR__) . '/.env.local')) {
    $dotenv->loadEnv(dirname(__DIR__) . '/.env.local');
} else {
    $dotenv->loadEnv(dirname(__DIR__) . '/.env');
}

$app = new App($dotenv);
$request = (new Request())->create();
$response = $app->handle($request);
$response->send();
$app->terminate($request, $response);
