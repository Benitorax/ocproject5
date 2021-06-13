<?php

namespace App\Tests\Controller\Fixtures;

class FixturesData
{
    public const USERS = [
        // username, email, password, isBlocked, isAdmin
        ['Sacha', 'sacha@mail.com', '123456', false, false],
        ['Ross', 'ross@mail.com', '123456', false, false],
        ['Matthew', 'matthew@mail.com', '123456', false, false],
        ['Monica', 'monica@mail.com', '123456', true, false],
        ['Mike', 'mike@mail.com', '123456', false, true],
        ['John', 'john@mail.com', '123456', false, true],
        ['Rachel', 'rachel@mail.com', '123456', false, true]
    ];

    public const POSTS = [
        // title, lead, content, isPublished
        [
            'The Published Post Title 1',
            'The lead of the post whose title is The Published Post Title 1.',
            'The first sentence of the content of the post whose title is The Published Post Title 1.' .
            'The second sentence of the content of the post whose title is The Published Post Title 1.',
            true
        ],
        [
            'The Published Post Title 2',
            'The lead of the post whose title is The Published Post Title 2.',
            'The first sentence of the content of the post whose title is The Published Post Title 2.' .
            'The second sentence of the content of the post whose title is The Published Post Title 2.',
            true
        ],
        [
            'The Unpublished Post Title 1',
            'The lead of the post whose title is The Unpublished Post Title 1.',
            'The first sentence of the content of the post whose title is The Unpublished Post Title 1.' .
            'The second sentence of the content of the post whose title is The Unpublished Post Title 1.',
            false
        ],
        [
            'The Unpublished Post Title 2',
            'The lead of the post whose title is The Unpublished Post Title 2.',
            'The first sentence of the content of the post whose title is The Unpublished Post Title 2.' .
            'The second sentence of the content of the post whose title is The Unpublished Post Title 2.',
            false
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
