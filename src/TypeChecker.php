<?php
/**
 * Created by PhpStorm.
 * User: inventor
 * Date: 16.09.2016
 * Time: 23:29
 */

namespace NewInventor\TypeChecker;


use NewInventor\TypeChecker\Exception\ArgumentTypeException;

class TypeChecker
{
    /** @var string|null */
    protected $file;
    /** @var string|null */
    protected $line;
    /** @var string|null */
    protected $class;
    /** @var string|null */
    protected $function;
    /** @var string|null */
    protected $type;
    protected $value;
    /** @var int */
    protected $index;
    /** @var bool */
    protected $isValid = false;
    /** @var bool */
    protected $inner = false;
    /** @var array */
    protected $types = [];
    /** @var array */
    protected $innerTypes = [];
    
    /**
     * NewTypeChecker constructor.
     *
     * @param array $backTraceData
     * @param int   $paramIndex
     */
    public function __construct(array $backTraceData, $paramIndex)
    {
        $this->line = $backTraceData['line'];
        $this->file = $backTraceData['file'];
        $this->class = $backTraceData['class'];
        $this->function = $backTraceData['function'];
        $this->type = $backTraceData['type'];
        $this->index = $paramIndex;
        $this->value = $backTraceData['args'][$paramIndex];
    }
    
    /**
     * @param callable $callback
     *
     * @return TypeChecker
     */
    public function callback(callable $callback)
    {
        $this->isValid = $this->isValid || $callback($this->value);
        
        return $this;
    }
    
    /**
     * @param string $name
     *
     * @return TypeChecker
     */
    protected function checkSimple($name)
    {
        $method = "is_$name";
        $this->types[] = $name;
        $this->isValid = $this->isValid || $method($this->value);
        
        return $this;
    }
    
    /**
     * @param string $name
     *
     * @return TypeChecker
     */
    protected function checkSimpleArray($name)
    {
        if (!is_array($this->value)) {
            return $this;
        }
        $method = "is_$name";
        $this->innerTypes[] = $name;
        $res = true;
        foreach ($this->value as $item) {
            $res = $res && $method($item);
        }
        $this->isValid = $this->isValid || $res;
        
        return $this;
    }
    
    /**
     * @return TypeChecker
     */
    public function inner()
    {
        $this->inner = true;
        
        return $this;
    }
    
    /**
     * @param string[] ...$types
     *
     * @return TypeChecker
     */
    public function types(...$types)
    {
        if (count($types) === 0) {
            $this->isValid = true;
            
            return $this;
        }
        
        if ($this->inner && is_array($this->value)) {
            $res = true;
            $this->innerTypes = array_merge($this->innerTypes, $types);
            /** @noinspection ForeachSourceInspection */
            foreach ($this->value as $item) {
                $res = $res && $this->checkValueTypes($item, $types);
            }
            $this->isValid = $this->isValid || $res;
        } else {
            $this->types = array_merge($this->types, $types);
            $this->isValid = $this->isValid || $this->checkValueTypes($this->value, $types);
        }
        
        return $this;
    }
    
    protected function checkValueTypes($value, array $types)
    {
        $res = false;
        foreach ($types as $type) {
            $res = $res || is_a($value, $type);
        }
        
        return $res;
    }
    
    /**
     * @throws ArgumentTypeException
     */
    public function fail()
    {
        if (!$this->isValid) {
            throw new ArgumentTypeException($this);
        }
    }
    
    /**
     * @return bool
     */
    public function result()
    {
        return $this->isValid;
    }
    
    /**
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }
    
    /**
     * @return string
     */
    public function getLine()
    {
        return $this->line;
    }
    
    /**
     * @return string
     */
    protected function getFullFunctionName()
    {
        return "{$this->class}{$this->type}{$this->function}";
    }
    
    /**
     * @return string
     */
    public function getErrorMessage()
    {
        $typesStr = implode(', ', $this->types);
        $innerTypesStr = implode(', ', $this->innerTypes);
        $str = "Тип аргумента №{$this->index} в методе {$this->getFullFunctionName()} неверен. ";
        if ($typesStr !== '') {
            $str .= "Необходимые типы параметра: {$typesStr}. ";
        }
        if ($innerTypesStr !== '') {
            $str .= "Необходимые типы элементов параметра: {$innerTypesStr}. ";
        }
        $str .= 'Получен тип: ' . gettype($this->value) . '.';
        
        return $str;
    }
    
    /**
     * @return $this
     */
    public function tarray()
    {
        if ($this->inner) {
            $this->checkSimpleArray('array');
        } else {
            $this->checkSimple('array');
        }
        
        return $this;
    }
    
    /**
     * @return $this
     */
    public function tbool()
    {
        if ($this->inner) {
            $this->checkSimpleArray('bool');
        } else {
            $this->checkSimple('bool');
        }
        
        return $this;
    }
    
    /**
     * @return $this
     */
    public function tcallable()
    {
        if ($this->inner) {
            $this->checkSimpleArray('callable');
        } else {
            $this->checkSimple('callable');
        }
        
        return $this;
    }
    
    /**
     * @return $this
     */
    public function tdouble()
    {
        if ($this->inner) {
            $this->checkSimpleArray('double');
        } else {
            $this->checkSimple('double');
        }
        
        return $this;
    }
    
    /**
     * @return $this
     */
    public function tfloat()
    {
        if ($this->inner) {
            $this->checkSimpleArray('float');
        } else {
            $this->checkSimple('float');
        }
        
        return $this;
    }
    
    /**
     * @return $this
     */
    public function tint()
    {
        if ($this->inner) {
            $this->checkSimpleArray('int');
        } else {
            $this->checkSimple('int');
        }
        
        return $this;
    }
    
    /**
     * @return $this
     */
    public function tinteger()
    {
        if ($this->inner) {
            $this->checkSimpleArray('integer');
        } else {
            $this->checkSimple('integer');
        }
        
        return $this;
    }
    
    /**
     * @return $this
     */
    public function tlong()
    {
        if ($this->inner) {
            $this->checkSimpleArray('long');
        } else {
            $this->checkSimple('long');
        }
        
        return $this;
    }
    
    /**
     * @return $this
     */
    public function tnull()
    {
        if ($this->inner) {
            $this->checkSimpleArray('null');
        } else {
            $this->checkSimple('null');
        }
        
        return $this;
    }
    
    /**
     * @return $this
     */
    public function tnumeric()
    {
        if ($this->inner) {
            $this->checkSimpleArray('numeric');
        } else {
            $this->checkSimple('numeric');
        }
        
        return $this;
    }
    
    /**
     * @return $this
     */
    public function tobject()
    {
        if ($this->inner) {
            $this->checkSimpleArray('object');
        } else {
            $this->checkSimple('object');
        }
        
        return $this;
    }
    
    /**
     * @return $this
     */
    public function treal()
    {
        if ($this->inner) {
            $this->checkSimpleArray('real');
        } else {
            $this->checkSimple('real');
        }
        
        return $this;
    }
    
    /**
     * @return $this
     */
    public function tresource()
    {
        if ($this->inner) {
            $this->checkSimpleArray('resource');
        } else {
            $this->checkSimple('resource');
        }
        
        return $this;
    }
    
    /**
     * @return $this
     */
    public function tscalar()
    {
        if ($this->inner) {
            $this->checkSimpleArray('scalar');
        } else {
            $this->checkSimple('scalar');
        }
        
        return $this;
    }
    
    /**
     * @return $this
     */
    public function tstring()
    {
        if ($this->inner) {
            $this->checkSimpleArray('string');
        } else {
            $this->checkSimple('string');
        }
        
        return $this;
    }
}