<?php

namespace App\Service\Pagination;

use Countable;
use ArrayIterator;
use IteratorAggregate;
use App\Service\Pagination\PaginationDAOInterface as DAOInterface;

class Paginator implements IteratorAggregate, Countable
{
    private int $pageNumber;
    private float $pagesTotal;
    private int $offset;
    private int $limitPerPage;

    private DAOInterface $dao;

    private array $items = [];

    /**
     * Returns the pagination.
     */
    public function paginate(DAOInterface $dao, int $pageNumber, int $limitPerPage): self
    {
        if ($pageNumber <= 0) {
            return $this;
        }

        $this->pageNumber = $pageNumber;
        $this->limitPerPage = $limitPerPage;
        $this->offset = ($pageNumber - 1) * $limitPerPage;

        // set the DAO to execute both lines below
        $this->dao = $dao;

        $this->setPagesTotal();
        $this->executePDOStatement();

        return $this;
    }

    /**
     * Executes the query.
     */
    public function executePDOStatement(): void
    {
        $this->items = (array) $this->dao->getPaginationResult($this->offset, $this->limitPerPage);
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
        $publishedCount = $this->dao->getPaginationCount();

        $this->pagesTotal = ceil($publishedCount / $this->limitPerPage);
    }

    public function getOffset(): int
    {
        return $this->offset;
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
