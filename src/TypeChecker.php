<?php
/**
 * User: inventor
 * Date: 16.09.2016
 * Time: 23:29
 */

namespace NewInventor\TypeChecker;


use NewInventor\TypeChecker\Exception\TypeException;

class TypeChecker
{
    const TARRAY = 'array';
    const TBOOL = 'bool';
    const TCALLABLE = 'callable';
    const TFLOAT = 'float';
    const TINT = 'int';
    const TNULL = 'null';
    const TNUMERIC = 'numeric';
    const TOBJECT = 'object';
    const TRESOURCE = 'resource';
    const TSCALAR = 'scalar';
    const TSTRING = 'string';
    protected $value;
    /** @var bool */
    protected $isValid = false;
    /** @var bool[] */
    protected $isValidInner = [];
    /** @var bool */
    protected $inner = false;
    /** @var array */
    protected $types = [];
    /** @var array */
    protected $innerTypes = [];
    /** @var int */
    protected $invalidInner;
    /** @var TypeChecker */
    private static $instance;
    
    /**
     * @param $value
     *
     * @return TypeChecker
     */
    public function validate($value)
    {
        $this->isValid = false;
        $this->isValidInner = null;
        $this->types = [];
        $this->innerTypes = [];
        $this->value = $value;
        $this->inner = false;
        $this->invalidInner = null;
    
        return $this;
    }
    
    /**
     * @param $value
     *
     * @return TypeChecker
     */
    public static function check($value)
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
    
        return self::$instance->validate($value);
    }
    
    /**
     * @param callable $callback
     *
     * @return TypeChecker
     */
    public function callback(callable $callback)
    {
        if ($this->inner) {
            if (!is_array($this->value)) {
                return $this;
            }
            $this->innerTypes[] = 'callback';
            /** @noinspection ForeachSourceInspection */
            foreach ($this->value as $key => $item) {
                $subRes = $callback($item);
                if ($this->invalidInner === null && !$subRes) {
                    $this->invalidInner = $key;
                }
                $this->isValidInner[$key] = $this->isValidInner[$key] || $subRes;
            }
        } else {
            $this->isValid = $this->isValid || $callback($this->value);
        }
        
        return $this;
    }
    
    /**
     * @return TypeChecker
     */
    public function inner()
    {
    
        if (!array_key_exists(self::TARRAY, $this->types)) {
            $this->checkSimpleType(self::TARRAY);
        }
        $this->inner = true;
        if (is_array($this->value)) {
            foreach ($this->value as $key => $item) {
                $this->isValidInner[$key] = false;
            }
        }
        
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
            return $this;
        }
    
        if ($this->inner) {
            if (!is_array($this->value)) {
                return $this;
            }
            $this->innerTypes = array_merge($this->innerTypes, $types);
            /** @noinspection ForeachSourceInspection */
            foreach ($this->value as $key => $item) {
                $subRes = $this->checkValueTypes($item, $types);
                if ($this->invalidInner === null && !$subRes) {
                    $this->invalidInner = $key;
                }
                $this->isValidInner[$key] = $this->isValidInner[$key] || $subRes;
            }
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
     * @throws TypeException
     */
    public function fail()
    {
        if (!$this->result()) {
            throw new TypeException($this->value, $this->types, $this->innerTypes, $this->invalidInner);
        }
    }
    
    /**
     * @return bool
     */
    public function result()
    {
        $res = $this->isValid;
        if ($this->isValidInner !== null) {
            foreach ($this->isValidInner as $value) {
                $res = $res && $value;
            }
        }
    
        return $res;
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
    public function tbool()
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
    public function tfloat()
    {
        return $this->checkSimpleType(self::TFLOAT);
    }
    
    /**
     * @return TypeChecker
     */
    public function tint()
    {
        return $this->checkSimpleType(self::TINT);
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
            $this->checkArraySimple($type);
        } else {
            $this->isValid = $this->isValid || $this->checkSimple($type);
        }
        
        return $this;
    }
    
    /**
     * @param string $name
     *
     * @return void
     */
    protected function checkArraySimple($name)
    {
        if (!is_array($this->value)) {
            return;
        }
        $method = "is_$name";
        $this->innerTypes[] = $name;
        foreach ($this->value as $key => $item) {
            $subRes = $method($item);
            if ($this->invalidInner === null && !$subRes) {
                $this->invalidInner = $key;
            }
            $this->isValidInner[$key] =
                (isset($this->isValidInner[$key]) ? $this->isValidInner[$key] : false) || $subRes;
        }
    }
    
    /**
     * @param string $name
     *
     * @return bool
     */
    protected function checkSimple($name)
    {
        $this->types[$name] = $name;
        $method = "is_$name";
        
        return $method($this->value);
    }
}