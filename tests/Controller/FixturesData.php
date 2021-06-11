<?php

namespace App\Tests\Controller;

class FixturesData
{
    public const USERS = [
        // username, email, password, isBlocked
        ['Sacha', 'sacha@mail.com', '123456', false],
        ['Ross', 'ross@mail.com', '123456', false],
        ['Matthew', 'matthew@mail.com', '123456', false],
        ['Monica', 'monica@mail.com', '123456', true]
    ];

    public const ADMIN_USERS = [
        ['Mike', 'mike@mail.com', '123456', false],
        ['John', 'john@mail.com', '123456', false],
        ['Rachel', 'rachel@mail.com', '123456', false]
    ];

    public const PUBLISHED_POSTS = [
        [
            'The Published Post Title 1',
            'The lead of the post whose title is The Published Post Title 1.',
            'The first sentence of the content of the post whose title is The Published Post Title 1.' .
            'The second sentence of the content of the post whose title is The Published Post Title 1.',
        ],
        [
            'The Published Post Title 2',
            'The lead of the post whose title is The Published Post Title 2.',
            'The first sentence of the content of the post whose title is The Published Post Title 2.' .
            'The second sentence of the content of the post whose title is The Published Post Title 2.',
        ]
    ];

    public const UNPUBLISHED_POSTS = [
        [
            'The Unpublished Post Title 1',
            'The lead of the post whose title is The Unpublished Post Title 1.',
            'The first sentence of the content of the post whose title is The Unpublished Post Title 1.' .
            'The second sentence of the content of the post whose title is The Unpublished Post Title 1.',
        ],
        [
            'The Unpublished Post Title 2',
            'The lead of the post whose title is The Unpublished Post Title 2.',
            'The first sentence of the content of the post whose title is The Unpublished Post Title 2.' .
            'The second sentence of the content of the post whose title is The Unpublished Post Title 2.',
        ]
    ];

    public const COMMENTS = [
        // comment, isValidated
        ['This is the content of the comment 1.', true],
        ['This is the content of the comment 2.', true],
        ['This is the content of the comment 3.', true],
        ['This is the content of the comment 4.', false],
        ['This is the content of the comment 5.', false]
    ];
}
