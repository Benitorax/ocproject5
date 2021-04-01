<?php

/**
 * @return Route[]
 *
 * /uri-path => [
 *  'name' => 'page_name',
 *  'method' => 'GET', # it can be a table: ['GET', POST], or a string: 'PUT'. If none it will be 'GET' by default.
 *  'callable' => 'Class::method'
 * ]
 */

return [
    // Security
    '/logout' => [
        'name' => 'logout',
        'method' => 'POST',
        'callable' => 'App\Controller\SecurityController::logout'
    ],
    '/login' => [
        'name' => 'login',
        'method' => ['GET', 'POST'],
        'callable' => 'App\Controller\SecurityController::login'
    ],
    // Post
    '/post/{slug}' => [
        'name' => 'post_show',
        'method' => 'GET',
        'callable' => 'App\Controller\PostController::show'
    ],
    '/post' => [
        'name' => 'post_index',
        'callable' => 'App\Controller\PostController::index'
    ],
    // Admin
    '/admin/login' => [
        'name' => 'admin_login',
        'method' => ['GET', 'POST'],
        'callable' => 'App\Controller\Admin\SecurityController::login'
    ],
    // Admin > Post
    '/admin/post' => [
        'name' => 'admin_post_index',
        'callable' => 'App\Controller\Admin\AdminPostController::index'
    ],
    '/admin/post/create' => [
        'name' => 'admin_post_create',
        'method' => ['GET', 'POST'],
        'callable' => 'App\Controller\Admin\AdminPostController::create'
    ],
    '/admin/post/{uuid}/edit' => [
        'name' => 'admin_post_edit',
        'method' => ['GET', 'POST'],
        'callable' => 'App\Controller\Admin\AdminPostController::edit'
    ],
    '/admin/post/{uuid}/delete' => [
        'name' => 'admin_post_delete',
        'method' => 'POST',
        'callable' => 'App\Controller\Admin\AdminPostController::delete'
    ],
    // Admin > User
    '/admin/user' => [
        'name' => 'admin_user_index',
        'callable' => 'App\Controller\Admin\AdminUserController::index'
    ],
    '/admin/user/{uuid}/block' => [
        'name' => 'admin_user_block',
        'method' => 'POST',
        'callable' => 'App\Controller\Admin\AdminUserController::block'
    ],
    '/admin/user/{uuid}/unblock' => [
        'name' => 'admin_user_unblock',
        'method' => 'POST',
        'callable' => 'App\Controller\Admin\AdminUserController::unblock'
    ],
    // App
    '/register' => [
        'name' => 'register',
        'method' => ['GET', 'POST'],
        'callable' => 'App\Controller\AppController::register'
    ],
    '/terms_of_use' => [
        'name' => 'terms_of_use',
        'callable' => 'App\Controller\AppController::termsOfUse'
    ],
    '/' => [
        'name' => 'home',
        'method' => ['GET', 'POST'],
        'callable' => 'App\Controller\AppController::home'
    ],
    // Fixtures
    '/fixtures' => [
        'name' => 'fixtures',
        'callable' => 'App\Controller\FixturesController::load'
    ]
];
