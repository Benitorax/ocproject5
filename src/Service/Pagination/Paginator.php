<?php

namespace App\Service\Pagination;

use Countable;
use ArrayIterator;
use App\Model\Post;
use App\Model\User;
use App\DAO\PostDAO;
use App\DAO\UserDAO;
use App\Model\Comment;
use IteratorAggregate;
use App\DAO\CommentDAO;
use App\Service\Pagination\PaginationDAOInterface as DAOInterface;
use Framework\Container\Container;

class Paginator implements IteratorAggregate, Countable
{
    private int $pageNumber;
    private float $pagesTotal;
    private int $offset;
    private int $limitPerPage = 5;
    private Container $container;
    private DAOInterface $dao;
    private array $parameters = [];
    private array $items = [];


    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function getPageNumber(): int
    {
        return $this->pageNumber;
    }

    public function getPagesTotal(): float
    {
        return $this->pagesTotal;
    }

    public function setPagesTotal(): void
    {
        $publishedCount = $this->dao->getCountBy($this->parameters);

        $this->pagesTotal = ceil($publishedCount / $this->limitPerPage);
    }

    /**
     * Executes the query.
     */
    public function executePDOStatement(): void
    {
        $this->items = (array) $this->dao->getBy($this->parameters, [], [$this->offset, $this->limitPerPage]);
    }

    /**
     * Returns the pagination.
     */
    public function paginate(int $pageNumber, string $className, array $parameters): self
    {
        if ($pageNumber <= 0) {
            return $this;
        }

        $this->pageNumber = $pageNumber;
        $this->offset = ($pageNumber - 1) * $this->limitPerPage;
        $this->parameters = $parameters;

        // set the provider before to set pages total
        $this->setProvider($className);

        // need the provider to execute both lines
        $this->setPagesTotal();
        $this->executePDOStatement();

        return $this;
    }

    /**
     * Set the DAO for the PDOStatement.
     */
    public function setProvider(string $className): void
    {
        switch ($className) {
            case Post::class:
                /** @var DAOInterface*/
                $dao = $this->container->get(PostDAO::class);
                $this->dao = $dao;
                break;

            case User::class:
                /** @var DAOInterface */
                $dao = $this->container->get(UserDAO::class);
                $this->dao = $dao;
                break;

            case Comment::class:
                /** @var DAOInterface */
                $dao = $this->container->get(CommentDAO::class);
                $this->dao = $dao;
                break;
        }
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function count(): int
    {
        return count($this->items);
    }

    /**
     * Returns an iterator for items.
     *
     * @return ArrayIterator An \ArrayIterator instance
     */
    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }
}
