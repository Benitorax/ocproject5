<?php

namespace App\Tests\Controller\Admin;

use App\Model\Comment;
use App\DAO\CommentDAO;
use App\Tests\Controller\AppWebTestCase;

class DashboardControllerTest extends AppWebTestCase
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

    public function testIndexWhenNotAdmin(): void
    {
        self::$client->loginUser(self::$users['user']['Sacha']);
        self::$client->request('GET', '/admin/dashboard');
        $this->assertResponseIsError();
    }

    public function testIndex(): void
    {
        // should redirect to comments page and display comments to validate
        self::$client->request('GET', '/admin/dashboard');
        self::$client->followRedirect();
        $this->assertTextContains('td', $this->comments[0]->getContent());
        $this->assertTextContains('td', $this->comments[1]->getContent());
    }

    public function testShowDraftPosts(): void
    {
        self::$client->request('GET', '/admin/dashboard/draft');
        $this->assertTextContains('td', self::$posts['unpublished'][0]->getTitle());
        $this->assertTextContains('td', self::$posts['unpublished'][0]->getTitle());

        // should go to edit page when click on edit button
        self::$client->clickLink('post-edit-' . self::$posts['unpublished'][0]->getUuid());
        $this->assertTextContainsForm('post');
    }

    public function testShowComments(): void
    {
        self::$client->request('GET', '/admin/dashboard/comments');
        $this->assertTextContains('td', $this->comments[0]->getContent());
        $this->assertTextContains('td', $this->comments[1]->getContent());

        // should validate by clicking on validate button
        self::$client->submitForm('comment-validate-' . $this->comments[0]->getUuid());
        $this->assertTextNotContains('td', $this->comments[0]->getContent());
    }
}
