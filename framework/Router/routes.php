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
    '/password/reset-request' => [
        'name' => 'password_reset_request',
        'method' => ['GET', 'POST'],
        'callable' => 'App\Controller\SecurityController::resetPasswordRequest'
    ],
    '/password/reset/{token}' => [
        'name' => 'password_reset',
        'method' => ['GET', 'POST'],
        'callable' => 'App\Controller\SecurityController::resetPassword'
    ],
    // Post
    '/post/{slug}' => [
        'name' => 'post_show',
        'method' => ['GET', 'POST'],
        'callable' => 'App\Controller\PostController::show'
    ],
    '/posts' => [
        'name' => 'post_index',
        'callable' => 'App\Controller\PostController::index'
    ],
    // Admin > Dashboard
    '/admin/dashboard' => [
        'name' => 'admin_dashboard',
        'callable' => 'App\Controller\Admin\DashboardController::index'
    ],
    '/admin/dashboard/draft' => [
        'name' => 'admin_dashboard_post_draft',
        'method' => ['GET', 'POST'],
        'callable' => 'App\Controller\Admin\DashboardController::showDraftPosts'
    ],
    '/admin/dashboard/comments' => [
        'name' => 'admin_dashboard_comment',
        'method' => ['GET', 'POST'],
        'callable' => 'App\Controller\Admin\DashboardController::showComments'
    ],
    '/admin/dashboard/comment/{uuid}/validate' => [
        'name' => 'admin_comment_validate',
        'method' => ['GET', 'POST'],
        'callable' => 'App\Controller\Admin\CommentController::validate'
    ],
    // Admin > Post
    '/admin/posts' => [
        'name' => 'admin_post_index',
        'callable' => 'App\Controller\Admin\PostController::index'
    ],
    '/admin/post/create' => [
        'name' => 'admin_post_create',
        'method' => ['GET', 'POST'],
        'callable' => 'App\Controller\Admin\PostController::create'
    ],
    '/admin/post/{uuid}/edit' => [
        'name' => 'admin_post_edit',
        'method' => ['GET', 'POST'],
        'callable' => 'App\Controller\Admin\PostController::edit'
    ],
    '/admin/post/{uuid}/delete' => [
        'name' => 'admin_post_delete',
        'method' => 'POST',
        'callable' => 'App\Controller\Admin\PostController::delete'
    ],
    // Admin > User
    '/admin/users' => [
        'name' => 'admin_user_index',
        'callable' => 'App\Controller\Admin\UserController::index'
    ],
    '/admin/user/{uuid}/block' => [
        'name' => 'admin_user_block',
        'method' => 'POST',
        'callable' => 'App\Controller\Admin\UserController::block'
    ],
    '/admin/user/{uuid}/unblock' => [
        'name' => 'admin_user_unblock',
        'method' => 'POST',
        'callable' => 'App\Controller\Admin\UserController::unblock'
    ],
    '/admin/user/{uuid}/delete' => [
        'name' => 'admin_user_delete',
        'method' => 'POST',
        'callable' => 'App\Controller\Admin\UserController::delete'
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
