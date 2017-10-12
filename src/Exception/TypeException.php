<?php
/**
 * User: Ionov George
 * Date: 15.02.2016
 * Time: 15:34
 */

namespace NewInventor\TypeChecker\Exception;


class TypeException extends \Exception
{
    /**
     * @var array
     */
    protected $backtrace;
    /**
     * @var mixed
     */
    protected $value;
    /**
     * @var array
     */
    protected $types = [];
    /**
     * @var array
     */
    protected $innerTypes = [];
    /** @var int */
    private $invalidInner;
    
    /**
     * ArgumentException constructor.
     *
     * @param mixed $value
     * @param array $types
     * @param array $innerTypes
     * @param int   $invalidInner
     *
     * @internal param array $backtrace
     */
    public function __construct($value, array $types, array $innerTypes = [], $invalidInner = 0)
    {
        $this->backtrace = debug_backtrace(null, 3)[2];
        $this->value = $value;
        $this->types = $types;
        $this->innerTypes = $innerTypes;
        $this->invalidInner = $invalidInner;
        parent::__construct($this->getErrorMessage());
    }
    
    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->getBaseErrorMessage() .
               $this->getValueErrorMessage() .
               $this->getElementErrorMessage();
    }
    
    protected function getValueErrorMessage()
    {
        if (!empty($this->types)) {
            $typesStr = implode(', ', $this->types);
            
            return "\nRequired type{$this->isOrAreType($this->types)}: {$typesStr} \nType received: " .
                   gettype($this->value);
        }
        
        return '';
    }
    
    protected function getElementErrorMessage()
    {
        if (!empty($this->innerTypes)) {
            $innerTypesStr = implode(', ', $this->innerTypes);
            
            return "\nRequired elements type{$this->isOrAreType($this->innerTypes)}: {$innerTypesStr}\nType received: " .
                   gettype($this->value[$this->invalidInner]);
        }
        
        return '';
    }
    
    /**
     * @return string
     */
    protected function getBaseErrorMessage()
    {
        return "The type of the variable in the method {$this->getFullFunctionName()} is incorrect.";
    }
    
    /**
     * @return string
     */
    protected function getFullFunctionName()
    {
        return "{$this->backtrace['class']}{$this->backtrace['type']}{$this->backtrace['function']}";
    }
    
    /**
     * @param $types
     *
     * @return string
     */
    protected function isOrAreType($types)
    {
        return (count($types) === 1 ? ' is' : 's are');
    }
}