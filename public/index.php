<?php

require dirname(__DIR__) . '/vendor/autoload.php';
// Comment or delete the line below for production
require_once dirname(__DIR__) . '/framework/Debug/Debug.php';

use Framework\App;
use Framework\Request\Request;

$request = (new Request())->create();
$app = new App();

if (file_exists(dirname(__DIR__) . '/.env.local')) {
    $app->addEnvVariables(dirname(__DIR__) . '/.env.local');
} else {
    $app->addEnvVariables(dirname(__DIR__) . '/.env');
}

$response = $app->handle($request);
$response->send();
$app->terminate();
