<?php

namespace App\Tests\Controller;

use App\Service\Mailer\Event\MailEvent;
use App\Service\Mailer\Subscriber\MailerSubscriber;

trait AppTestAssertionsTrait
{
    public static function assertEmailCount(int $expectedCount): void
    {
        $count = count(self::getMailEvents());
        self::assertSame($expectedCount, $count, sprintf(
            'The email count should be %d, but %d given.',
            $expectedCount,
            $count
        ));
    }

    public static function assertQueuedEmailCount(int $expectedCount): void
    {
        $count = count(self::getMailEvents());
        self::assertSame($expectedCount, $count, sprintf(
            'The queued email count should be %d, but %d given.',
            $expectedCount,
            $count
        ));
    }

    /**
     * @return MailEvent[]
     */
    public static function getMailEvents()
    {
        return self::$client->getContainer()->get(MailerSubscriber::class)->getMailEvents();
    }
}
