<?php
require dirname(__DIR__).'/vendor/autoload.php';
// Comment or delete the line below for production
require_once dirname(__DIR__).'/config/Debug/Debug.php';

use Config\Container\Container;
use Config\Router\Request;

session_start();

$request = (new Request)->create();
$container = new Container();

$router = $container->getRouter();
$router->run($request);