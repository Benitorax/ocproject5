<?php
namespace App\Service\Validation;

use Exception;
use App\DAO\DAO;

class Constraint
{
    private $DAO;

    public function __construct(DAO $DAO)
    {
        $this->DAO = $DAO;
    }
    
    public function validate($constraint, $value, $name = null)
    {
        if (is_array($constraint)) {
            $callable = [$this, $constraint[0]];
            
            if (!is_callable($callable)) {
                throw new Exception(
                    sprintf('The method \'%s\' is not found in Constraint class.', $constraint[0]),
                    500
                );
            }
            return $callable($constraint, $value, $name);
        }
    }
    
    public function notBlank($constraint, $value, $name = null)
    {
        if (empty($value)) {
            if (!empty($name)) {
                return 'The field "'.$name.'" should not be empty';
            }
            return 'The field should not be empty';
        }
    }

    public function minLength($constraint, $value, $name = null)
    {
        $min = $constraint[1];

        if (strlen($value) < $min) {
            if (!empty($name)) {
                return 'The field "'.$name.'" should contain at least '.$min.' characters';
            }
            return 'The field should contain at least '.$min.' characters';
        }
    }
    
    public function maxLength($constraint, $value, $name = null)
    {
        $max = $constraint[1];

        if (strlen($value) > $max) {
            if (!empty($name)) {
                return 'The field "'.$name.'" should not contain more than '.$max.' characters';
            }
            return 'The field should not contain more than '.$max.' characters';
        }
    }

    public function unique($constraint, $value, $name = null)
    {
        [$table, $colName] = explode(':', $constraint[1], 2);
        $count = $this->DAO->getCountBy($table, $colName, $value);

        if ($count > 0) {
            if (!empty($name)) {
                return 'The '.$name.' "'.$value.'" already exists';
            }
            return 'The field with value "'.$value.'" already exists';
        }
    }

    public function identical($value1, $value2, $name = null)
    {
        if (strtolower($value1) !== strtolower($value2)) {
            if (!empty($name)) {
                return 'The '.$name.' should be the same in both field';
            }
            return 'Both fields should have the same value';
        }
    }

    public function checkbox($constraint, $value, $name = null)
    {
        if ($constraint[1] !== $value) {
            if ($constraint[1] === true) {
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
    }
}
