<?php
/**
 * @return Route[]
 * /uri-path => [
 *  'name' => 'page_name',
 *  'method' => 'GET', # it can be a table like ['GET', POST], a string like 'PUT'. If nothing it will be only 'GET'.
 *  'callable' => 'Class::method'
 * ]
 */
return [
    '/logout' => [
        'name' => 'logout',
        'method' => 'POST',
        'callable' => 'App\Controller\AppController::logout'
    ],
    '/login' => [
        'name' => 'login',
        'method' => ['GET', 'POST'],
        'callable' => 'App\Controller\AppController::login'
    ],
    '/register' => [
        'name' => 'register',
        'method' => ['GET', 'POST'],
        'callable' => 'App\Controller\AppController::register'
    ],
    '/terms_of_use' => [
        'name' => 'terms_of_use',
        'callable' => 'App\Controller\AppController::termsOfUse'
    ],
    '/post/{slug}/author/{username}' => [
        'name' => 'post',
        'method' => 'GET',
        'callable' => 'App\Controller\AppController::post'
    ],
    '/' => [
        'name' => 'home',
        'method' => ['GET', 'POST'],
        'callable' => 'App\Controller\AppController::home'
    ]
];
