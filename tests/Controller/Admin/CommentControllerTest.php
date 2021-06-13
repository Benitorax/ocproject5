<?php

namespace App\Tests\Controller\Admin;

use App\Model\Comment;
use App\DAO\CommentDAO;
use App\Tests\Controller\AppWebTestCase;

class CommentControllerTest extends AppWebTestCase
{
    /**
     * @var Comment[]
     */
    public $comments;

    public function setup(): void
    {
        parent::setup();
        self::$client->loginUser(self::$users['admin']['Mike']);
        /** @var Comment[] */
        $comments = self::$client->getContainer()->get(CommentDAO::class)->getCommentsToValidate();
        $this->comments = $comments;
    }

    public function testValidateWhenNotAdmin(): void
    {
        self::$client->loginUser(self::$users['user']['Sacha']);
        self::$client->request('POST', '/admin/dashboard/comment/' . $this->comments[0]->getUuid() . '/validate');
        $this->assertResponseIsError();
    }

    public function testDeleteWhenNotAdmin(): void
    {
        self::$client->loginUser(self::$users['user']['Sacha']);
        self::$client->request('POST', '/admin/dashboard/comment/' . $this->comments[0]->getUuid() . '/delete');
        $this->assertResponseIsError();
    }

    public function testValidate(): void
    {
        $comment = $this->comments[0];

        // comment should appear in dashboard before deletion
        self::$client->request('POST', '/admin/dashboard/comments');
        $this->assertTextContains('td', $comment->getContent());

        // submits incorrectly
        self::$client->request('POST', '/admin/dashboard/comment/' . $comment->getUuid() . '/validate');
        self::$client->followRedirect();
        $this->assertTextContains('td', $comment->getContent());

        // submits correctly
        self::$client->request('POST', '/admin/dashboard/comment/' . $comment->getUuid() . '/validate', [
            'csrf_token' => self::$client->getCsrfToken()
        ]);
        self::$client->followRedirect();

        $this->assertTextNotContains('td', $comment->getContent());
        $this->assertTextContains('div', 'The comment has been validated with success!');
    }

    public function testDelete(): void
    {
        $comment = $this->comments[0];

        // comment should appear in dashboard before deletion
        self::$client->request('POST', '/admin/dashboard/comments');
        $this->assertTextContains('td', $comment->getContent());

        // submits incorrectly
        self::$client->request('POST', '/admin/dashboard/comment/' . $comment->getUuid() . '/delete');
        self::$client->followRedirect();
        $this->assertTextContains('td', $comment->getContent());

        // submits correctly
        self::$client->request('POST', '/admin/dashboard/comment/' . $comment->getUuid() . '/delete', [
            'csrf_token' => self::$client->getCsrfToken()
        ]);
        self::$client->followRedirect();

        $this->assertTextNotContains('td', $comment->getContent());
        $this->assertTextContains('div', 'The comment has been deleted with success!');
    }
}
