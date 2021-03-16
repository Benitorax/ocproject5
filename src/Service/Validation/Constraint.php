<?php

namespace App\Service\Validation;

use Exception;
use App\DAO\DAO;

/**
 * This class helps Validation to return error messages.
 */
class Constraint
{
    private DAO $dao;

    public function __construct(DAO $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Calls one of the Constraint's method. The callable is a method of this Constraint.
     *
     * @param bool|string|int $value
     */
    public function validate(array $constraint, $value, ?string $name = null): ?string
    {
        $callable = [$this, $constraint[0]];

        if (!is_callable($callable)) {
            throw new Exception(
                sprintf('The method \'%s\' is not found in Constraint class.', $constraint[0]),
                500
            );
        }

        return $callable($constraint, $value, $name);
    }

    public function notBlank(array $constraint, string $value, string $name = null): ?string
    {
        if (empty($value)) {
            if (!empty($name)) {
                return 'The field "' . $name . '" should not be empty';
            }
            return 'The field should not be empty';
        }

        return null;
    }

    public function minLength(array $constraint, string $value, string $name = null): ?string
    {
        $min = $constraint[1];

        if (strlen($value) < $min) {
            if (!empty($name)) {
                return 'The field "' . $name . '" should contain at least ' . $min . ' characters';
            }
            return 'The field should contain at least ' . $min . ' characters';
        }

        return null;
    }

    public function maxLength(array $constraint, string $value, string $name = null): ?string
    {
        $max = $constraint[1];

        if (strlen($value) > $max) {
            if (!empty($name)) {
                return 'The field "' . $name . '" should not contain more than ' . $max . ' characters';
            }
            return 'The field should not contain more than ' . $max . ' characters';
        }

        return null;
    }

    /**
     * Checks if the value is a valid email format.
     */
    public function email(array $constraint, string $value, string $name = null): ?string
    {
        if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            if (!empty($name)) {
                return 'The field "' . $name . '" has not a valid format';
            }
            return 'Invalid email address';
        }

        return null;
    }

    /**
     * Checks if the value is unique in the database.
     */
    public function unique(array $constraint, string $value, string $name = null): ?string
    {
        [$table, $colName] = explode(':', $constraint[1], 2);
        $count = $this->dao->getCountBy($table, $colName, $value);

        if ($count > 0) {
            if (!empty($name)) {
                return 'The ' . $name . ' "' . $value . '" already exists';
            }
            return 'The field with value "' . $value . '" already exists';
        }

        return null;
    }

    /**
     * Checks if two values are identical.
     */
    public function identical(string $value1, string $value2, string $name = null): ?string
    {
        if (strtolower($value1) !== strtolower($value2)) {
            if (!empty($name)) {
                return 'The ' . $name . ' should be the same in both field';
            }
            return 'Both fields should have the same value';
        }

        return null;
    }

    /**
     * Checks if the checkbox is checked or unchecked.
     */
    public function checkbox(array $constraint, string $value, string $name = null): ?string
    {
        if ((bool) $constraint[1] !== (bool) $value) {
            if (true === (bool) $constraint[1]) {
                if (!empty($name)) {
                    return 'The box "' . $name . '" must be checked';
                }
                return 'The box must be checked';
            } else {
                if (!empty($name)) {
                    return 'The box "' . $name . '" must be unchecked';
                }
                return 'The box must be unchecked';
            }
        }

        return null;
    }
}
