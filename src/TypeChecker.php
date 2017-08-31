<?php
/**
 * User: inventor
 * Date: 16.09.2016
 * Time: 23:29
 */

namespace NewInventor\TypeChecker;


use NewInventor\TypeChecker\Exception\ArgumentTypeException;
use NewInventor\TypeChecker\Exception\VariableTypeException;

class TypeChecker
{
    const TARRAY = 'array';
    const TBOOLEAN = 'boolean';
    const TCALLABLE = 'callable';
    const TDOUBLE = 'double';
    const TINTEGER = 'integer';
    const TNULL = 'null';
    const TNUMERIC = 'numeric';
    const TOBJECT = 'object';
    const TRESOURCE = 'resource';
    const TSCALAR = 'scalar';
    const TSTRING = 'string';
    /** @var string */
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
    private $backtrace;
    
    /**
     * TypeChecker constructor.
     *
     * @param $value
     */
    public function __construct($value)
    {
        $this->value = $value;
        $this->type = gettype($value);
    }
    
    public function setBackTrace(array $backTrace)
    {
        $this->backtrace = $backTrace;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
    
    /**
     * @param null|int $index
     *
     * @return TypeChecker
     * @throws \NewInventor\TypeChecker\Exception\ArgumentTypeException
     */
    public function setIndex($index = null)
    {
        if(!is_numeric($index)){
            throw new ArgumentTypeException(debug_backtrace(null, 1)[0], 0, $index, ['int']);
        }
        $this->index = (int)$index;
        
        return $this;
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
    
    /**
     * @param       $value
     * @param array $types
     *
     * @return bool
     */
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
     * @throws VariableTypeException
     */
    public function fail()
    {
        if (!$this->isValid) {
            if ($this->index === null) {
                throw new ArgumentTypeException($this->backtrace, $this->index, $this->value, $this->types, $this->innerTypes);
            }
            throw new VariableTypeException($this->backtrace, $this->value, $this->types, $this->innerTypes);
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
     * @return TypeChecker
     */
    public function tarray()
    {
        return $this->checkSimpleType(self::TARRAY);
    }
    
    /**
     * @return TypeChecker
     */
    public function tboolean()
    {
        return $this->checkSimpleType(self::TBOOL);
    }
    
    /**
     * @return TypeChecker
     */
    public function tcallable()
    {
        return $this->checkSimpleType(self::TCALLABLE);
    }
    
    /**
     * @return TypeChecker
     */
    public function tdouble()
    {
        return $this->checkSimpleType(self::TDOUBLE);
    }
    
    /**
     * @return TypeChecker
     */
    public function tinteger()
    {
        return $this->checkSimpleType(self::TINTEGER);
    }
    
    /**
     * @return TypeChecker
     */
    public function tnull()
    {
        return $this->checkSimpleType(self::TNULL);
    }
    
    /**
     * @return TypeChecker
     */
    public function tnumeric()
    {
        return $this->checkSimpleType(self::TNUMERIC);
    }
    
    /**
     * @return TypeChecker
     */
    public function tobject()
    {
        return $this->checkSimpleType(self::TOBJECT);
    }
    
    /**
     * @return TypeChecker
     */
    public function tresource()
    {
        return $this->checkSimpleType(self::TRESOURCE);
    }
    
    /**
     * @return TypeChecker
     */
    public function tscalar()
    {
        return $this->checkSimpleType(self::TSCALAR);
    }
    
    /**
     * @return TypeChecker
     */
    public function tstring()
    {
        return $this->checkSimpleType(self::TSTRING);
    }
    
    protected function checkSimpleType($type)
    {
        if ($this->inner) {
            $res = $this->checkArraySimple($type);
            if($res){
                $this->type = $type;
            }
            $this->isValid = $this->isValid || $res;
        } else {
            $res = $this->checkSimple($type);
            if($res){
                $this->type = $type;
            }
            $this->isValid = $this->isValid || $res;
        }
        
        return $this;
    }
    
    /**
     * @param string $name
     *
     * @return bool
     */
    protected function checkArraySimple($name)
    {
        if (!is_array($this->value)) {
            return true;
        }
        $method = "is_$name";
        $this->innerTypes[] = $name;
        $res = true;
        foreach ($this->value as $item) {
            $res = $res && $method($item);
        }
        
        return $res;
    }
    
    /**
     * @param string $name
     *
     * @return bool
     */
    protected function checkSimple($name)
    {
        $this->types[] = $name;
        $method = "is_$name";
        
        return $method($this->value);
    }
}