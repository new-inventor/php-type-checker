<?php
/**
 * User: Ionov George
 * Date: 21.03.2016
 * Time: 13:10
 */

namespace NewInventor\TypeChecker;

use NewInventor\Singleton\SingletonTrait;
use NewInventor\TypeChecker\Exception\ArgumentException;
use NewInventor\TypeChecker\Exception\ArgumentTypeException;

class TypeChecker
{
    use SingletonTrait;

    /** @var string */
    protected $lastCheckedName;
    /** @var mixed */
    protected $lastCheckedValue;
    /** @var string[] */
    protected $lastCheckedTypes;

    /** @var bool */
    protected $isValid = false;

    /**
     * @param string $name
     * @param mixed $value
     * @return TypeChecker
     * @throws ArgumentTypeException
     */
    public function isBool($value, $name = '')
    {
        return $this->check($value, [SimpleTypes::BOOL], $name);
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return TypeChecker
     * @throws ArgumentTypeException
     */
    public function isString($value, $name = '')
    {
        return $this->check($value, [SimpleTypes::STRING], $name);
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return TypeChecker
     * @throws ArgumentTypeException
     */
    public function isInt($value, $name = '')
    {
        return $this->check($value, [SimpleTypes::INT], $name);
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return TypeChecker
     * @throws ArgumentTypeException
     */
    public function isArr($value, $name = '')
    {
        return $this->check($value, [SimpleTypes::ARR], $name);
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return TypeChecker
     * @throws ArgumentTypeException
     */
    public function isFloat($value, $name = '')
    {
        return $this->check($value, [SimpleTypes::FLOAT], $name);
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return TypeChecker
     * @throws ArgumentTypeException
     */
    public function isObj($value, $name = '')
    {
        return $this->check($value, [SimpleTypes::OBJ], $name);
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return TypeChecker
     * @throws ArgumentTypeException
     */
    public function isResource($value, $name = '')
    {
        return $this->check($value, [SimpleTypes::RESOURCE], $name);
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return TypeChecker
     * @throws ArgumentTypeException
     */
    public function isNull($value, $name = '')
    {
        return $this->check($value, [SimpleTypes::NULL], $name);
    }

    /**
     * @param mixed $value
     * @param string[] $expectedTypes
     * @param string $name
     * @return TypeChecker
     * @throws ArgumentTypeException
     */
    public function check($value, array $expectedTypes = [], $name = '')
    {
        if (!is_array($expectedTypes)) {
            $this->isValid = false;

            return $this;
        }
        if (empty($expectedTypes)) {
            $this->isValid = true;

            return $this;
        }
        if (!is_string($name)) {
            throw new ArgumentTypeException('name', [SimpleTypes::STRING], $name);
        }

        $this->initLast($value, $expectedTypes, $name);

        $res = false;
        foreach ($expectedTypes as $type) {
            $res = $res || (gettype($value) == $type) || is_a($value, $type);
        }
        $this->isValid = $res;

        return $this;
    }

    protected function initLast($value, $types, $name)
    {
        $this->lastCheckedName = $name;
        $this->lastCheckedValue = $value;
        $this->lastCheckedTypes = $types;
    }


    /**
     * @param array $elements
     * @param string[] $expectedTypes
     * @param string $name
     * @return TypeChecker
     */
    public function checkArray(array $elements, array $expectedTypes = [], $name = '')
    {
        if (empty($expectedTypes)) {
            return true;
        }
        $res = true;
        foreach ($elements as $el) {
            $res = $res && $this->check($el, $expectedTypes)->result();
        }
        $this->initLast($elements, $expectedTypes, $name);
        $this->isValid = $res;

        return $this;
    }

    /**
     * @throws ArgumentTypeException
     */
    public function throwTypeErrorIfNotValid(){
        if(!$this->isValid){
            $this->throwTypeError();
        }
    }

    /**
     * @throws ArgumentTypeException
     */
    public function throwTypeError()
    {
        throw new ArgumentTypeException($this->lastCheckedName, $this->lastCheckedTypes, $this->lastCheckedValue);
    }

    /**
     * @param string $message
     * @throws ArgumentException
     */
    public function throwCustomErrorIfNotValid($message = '')
    {
        if(!$this->isValid) {
            $this->throwCustomError($message);
        }
    }

    /**
     * @param string $message
     * @throws ArgumentException
     */
    public function throwCustomError($message = '')
    {
        throw new ArgumentException($message, $this->lastCheckedName);
    }

    /**
     * @return bool
     */
    public function result()
    {
        return $this->isValid;
    }

}