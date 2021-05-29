<?php

/**
 * @return array of metadata
 *
 * alias => ['fullyQualifiedClassName/which/is/type-hinted' => 'fullyQualifiedClassName/which/replace']
 */

return [
    'alias' => [
        // Security
        // Define the DAO class to fetch user for authentication
        Framework\DAO\UserDAOInterface::class => App\DAO\UserDAO::class
    ],
    'event' => [
        'event.terminate' => [
            'listeners' => [
                // [listener::class, priority];
            ],
            'subscribers' => [
                App\Service\Mailer\MailerSubscriber::class
            ]
        ]
    ]
];
