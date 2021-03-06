<?php

/**
 * @return Framework\Router\Route[]
 *
 * /uri-path => [
 *  'name' => 'page_name',
 *  'method' => 'GET', # it can be a table: ['GET', POST], or a string: 'PUT'. If none it will be 'GET' by default.
 *  'callable' => 'Class::method'
 * ]
 *
 * Note: Only one wildcard is allowed between each slashes:
 * Allowed: "/{language}/{date}/{slug}".
 * Forbidden: "/{language}-{date}/{slug}-by-{author}".
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
        'callable' => 'App\Controller\PostController::show'
    ],
    '/posts' => [
        'name' => 'post_index',
        'callable' => 'App\Controller\PostController::index'
    ],
    // Comment
    '/post/{uuid}/comment/create' => [
        'name' => 'comment_create',
        'method' => 'POST',
        'callable' => 'App\Controller\CommentController::create'
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
    // Admin > Comment
    '/admin/dashboard/comment/{uuid}/validate' => [
        'name' => 'admin_comment_validate',
        'method' => 'POST',
        'callable' => 'App\Controller\Admin\CommentController::validate'
    ],
    '/admin/dashboard/comment/{uuid}/delete' => [
        'name' => 'admin_comment_delete',
        'method' => 'POST',
        'callable' => 'App\Controller\Admin\CommentController::delete'
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
    '/terms-of-use' => [
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
