<?php

namespace App\Service\Pagination;

use App\Model\Post;
use App\Model\User;
use App\Model\Comment;

/**
 * DAO class must implement this interface to have pagination.
 */
interface PaginationDAOInterface
{
    /**
     * Returns an array of Model objects.
     *
     * @return null|object[]|User[]|Comment[]|Post[]
     */
    public function getPaginationResult(int $offset, int $range);

    /**
     * Returns the count of items.
     *
     * @return int
     */
    public function getPaginationCount();
}
