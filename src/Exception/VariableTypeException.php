<?php
/**
 * User: Ionov George
 * Date: 15.02.2016
 * Time: 15:34
 */

namespace NewInventor\TypeChecker\Exception;


class VariableTypeException extends \Exception
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
    
    /**
     * ArgumentException constructor.
     *
     * @param array $backtrace
     * @param mixed   $value
     * @param array $types
     * @param array $innerTypes
     */
    public function __construct(array $backtrace, $value, array $types, array $innerTypes = [])
    {
        parent::__construct($this->getErrorMessage());
        $this->backtrace = $backtrace;
        $this->value = $value;
        $this->types = $types;
        $this->innerTypes = $innerTypes;
    }
    
    /**
     * @return string
     */
    public function getErrorMessage()
    {
        $str = $this->getBaseErrorMessage();
        if (!empty($this->types)) {
            $typesStr = implode(', ', $this->types);
            $str .= "\nRequired argument type{$this->isOrAreType()}: {$typesStr}. ";
        }
        if (!empty($this->innerTypes)) {
            $innerTypesStr = implode(', ', $this->innerTypes);
            $str .= "\nRequired argument elements type{$this->isOrAreType()}: {$innerTypesStr}. ";
        }
        $str .= PHP_EOL .
                'Type received: ' .
                gettype($this->value) .
                ".\nFile: {$this->backtrace['file']}\nLine: {$this->backtrace['line']}.";
        
        return $str;
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
     * @return string
     */
    protected function isOrAreType()
    {
        return (count($this->types) === 1 ? ' is' : 's are');
    }
}