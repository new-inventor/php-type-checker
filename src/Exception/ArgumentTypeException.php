<?php
/**
 * User: Ionov George
 * Date: 15.02.2016
 * Time: 15:34
 */

namespace NewInventor\TypeChecker\Exception;


class ArgumentTypeException extends VariableTypeException
{
    /**
     * @var int
     */
    protected $index;
    
    /**
     * ArgumentException constructor.
     *
     * @param array $backtrace
     * @param int   $index
     * @param mixed $value
     * @param array $types
     * @param array $innerTypes
     */
    public function __construct(array $backtrace, $index, $value, array $types, array $innerTypes = [])
    {
        parent::__construct($backtrace, $value, $types, $innerTypes);
        $this->index = $index;
    }
    
    protected function getBaseErrorMessage()
    {
        return "The type of the argument №{$this->index} in the method {$this->getFullFunctionName()} is incorrect.";
    }
}