<?php
namespace App\Service\Validation;

use Exception;
use App\DAO\DAO;

class Constraint
{
    private DAO $DAO;

    public function __construct(DAO $DAO)
    {
        $this->DAO = $DAO;
    }
    
    /**
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
                return 'The field "'.$name.'" should not be empty';
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
                return 'The field "'.$name.'" should contain at least '.$min.' characters';
            }
            return 'The field should contain at least '.$min.' characters';
        }

        return null;
    }
    
    public function maxLength(array $constraint, string $value, string $name = null): ?string
    {
        $max = $constraint[1];

        if (strlen($value) > $max) {
            if (!empty($name)) {
                return 'The field "'.$name.'" should not contain more than '.$max.' characters';
            }
            return 'The field should not contain more than '.$max.' characters';
        }
        
        return null;
    }

    public function unique(array $constraint, string $value, string $name = null): ?string
    {
        [$table, $colName] = explode(':', $constraint[1], 2);
        $count = $this->DAO->getCountBy($table, $colName, $value);

        if ($count > 0) {
            if (!empty($name)) {
                return 'The '.$name.' "'.$value.'" already exists';
            }
            return 'The field with value "'.$value.'" already exists';
        }
        
        return null;
    }

    public function identical(string $value1, string $value2, string $name = null): ?string
    {
        if (strtolower($value1) !== strtolower($value2)) {
            if (!empty($name)) {
                return 'The '.$name.' should be the same in both field';
            }
            return 'Both fields should have the same value';
        }
        
        return null;
    }

    public function checkbox(array $constraint, string $value, string $name = null): ?string
    {
        if ((bool) $constraint[1] !== (bool) $value) {
            if ((bool) $constraint[1] === (bool) true) {
                if (!empty($name)) {
                    return 'The box "'.$name.'" must be checked';
                }
                return 'The box must be checked';
            } else {
                if (!empty($name)) {
                    return 'The box "'.$name.'" must be unchecked';
                }
                return 'The box must be unchecked';
            }
        }
        
        return null;
    }
}
