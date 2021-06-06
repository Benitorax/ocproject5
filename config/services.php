<?php

/**
 * @return array of metadata
 *
 * alias => ['fullyQualifiedClassName/which/is/type-hinted' => 'fullyQualifiedClassName/which/replace']
 */

use Framework\EventDispatcher\Event\ExceptionEvent;
use Framework\EventDispatcher\Event\TerminateEvent;

return [
    'alias' => [
        // Security
        // Define the DAO class to fetch user for authentication
        Framework\DAO\UserDAOInterface::class => App\DAO\UserDAO::class
    ],
    'event' => [
        'events' => [
            TerminateEvent::class => [
                'listeners' => [
                    // [listener::class, priority],
                    // [EntityListener::class, 10],
                ]
            ],
            ExceptionEvent::class => [
                'listeners' => [
                    // [listener::class, priority],
                ]
            ]
        ],
        'subscribers' => [
            App\Service\Mailer\Subscriber\MailerSubscriber::class,
            Framework\Controller\ControllerSubscriber::class
        ]
    ]
];
