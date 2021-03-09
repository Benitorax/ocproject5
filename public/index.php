<?php
require dirname(__DIR__).'/vendor/autoload.php';
// Comment or delete the line below for production
require_once dirname(__DIR__).'/config/Debug/Debug.php';

use Config\App;
use Config\Request\Request;

$request = (new Request)->create();
$app = new App();

$response = $app->handle($request);
$response->send();
// $app->terminate();
