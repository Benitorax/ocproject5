<?php
namespace App\Service\Validation;

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
        if(is_array($constraint)) {
            $callable = [$this, $constraint[0]];
            
            if(!is_callable($callable)) {
                throw new \Exception(sprintf('The method \'%s\' is not found in Constraint class.', $constraint[0]), 500);
            } else {
                return $callable($constraint, $value, $name);
            }
        }
    }
    
    public function notBlank($constraint = null, $value, $name = null)
    {
        if(empty($value)) {
            if($name) {
                return 'The field "'.$name.'" should not be empty';
            } else {
                return 'The field should not be empty';
            }
        }
    }
    public function minLength($constraint = null, $value, $name = null)
    {
        $min = $constraint[1];

        if(strlen($value) < $min) {
            if($name) {
                return 'The field "'.$name.'" should contain at least '.$min.' characters';
            } else {
                return 'The field should contain at least '.$min.' characters';
            }
        }
    }
    public function maxLength($constraint = null, $value, $name = null)
    {
        $max = $constraint[1];

        if(strlen($value) > $max) {
            if($name) {
                return 'The field "'.$name.'" should not contain more than '.$max.' characters';
            } else {
                return 'The field should not contain more than '.$max.' characters';
            }
        }
    }

    public function unique($constraint = null, $value, $name = null)
    {
        [$table, $colName] = explode(':', $constraint[1], 2);

        $count = $this->DAO->getCountBy($table, $colName, $value);

        if($count > 0) {
            if($name) {
                return 'The '.$name.' "'.$value.'" already exists';
            } else {
                return 'The field with value "'.$value.'" already exists';
            }
        }
    }

    public function identical($value1, $value2, $name = null)
    {
        if(strtolower($value1) !== strtolower($value2)) {
            if($name) {
                return 'The '.$name.' should be the same in both field';
            } else {
                return 'Both fields should have the same value';
            }
        }
    }

    public function checkbox($constraint, $value, $name = null)
    {
        if($constraint[1] !== $value) {
            if($constraint[1] === true) {
                if($name) {
                    return 'The box "'.$name.'" must be checked';
                } else {
                    return 'The box must be checked';
                }
            } else {
                if($name) {
                    return 'The box "'.$name.'" must be unchecked';
                } else {
                    return 'The box must be unchecked';
                }
            }
        }
    }
}