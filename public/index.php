<?php
require dirname(__DIR__).'/vendor/autoload.php';

use Config\Container\Container;

session_start();
$container = new Container();
$router = $container->getRouter();
$router->setContainer($container);
$router->run();