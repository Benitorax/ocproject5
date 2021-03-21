<?php

namespace App\Service\Pagination;

use App\Model\Post;
use App\Model\User;
use App\Model\Comment;

/**
 * Must implement this interface if extends AbstractDAO.
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
