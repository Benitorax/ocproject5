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
     * @param array $parameters = ['id' => $id]
     * @param array $orderBy = ['updated_at' => 'DESC']
     * @param array $limit = [$offset, $range] = [10, 10]
     * @return null|object[]|User[]|Comment[]|Post[]
     */
    public function getBy(array $parameters, array $orderBy = [], array $limit = []);

    /**
     * Returns the count of items.
     *
     * @param array $parameters = ['id' => $id]

     * @return int
     */
    public function getCountBy(array $parameters);
}
