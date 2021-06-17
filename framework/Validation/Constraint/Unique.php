<?php

namespace Framework\Validation\Constraint;

use Framework\DAO\DAO;

class Unique extends Constraint
{
    protected string $defaultMessage = 'The field with value "%s" already exists';
    protected string $messageWithLabel = 'The %s "%s" already exists';
    protected DAO $dao;

    /**
     * SQL table and column, e.g.: user:username or user:email
     */
    protected string $tableColumn;

    public function __construct(array $options)
    {
        parent::__construct($options);
    }

    public function setDAO(DAO $dao): self
    {
        $this->dao = $dao;

        return $this;
    }

    public function validate($value): ?string
    {
        [$table, $column] = explode(':', $this->tableColumn, 2);
        $count = $this->dao->getCountBy($table, $column, $value);

        if ($count > 0) {
            return $this->getUniqueMessage($value);
        }

        return null;
    }

    public function getUniqueMessage(string $value): string
    {
        if (null !== $this->customMessage) {
            return $this->customMessage;
        }

        if (null !== $this->label) {
            return sprintf($this->messageWithLabel, $this->label, $value);
        }

        return sprintf($this->defaultMessage, $value);
    }
}
