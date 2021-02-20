<?php
require dirname(__DIR__).'/vendor/autoload.php';

use Config\Router\Router;

session_start();
$router = new Router();
$router->run();