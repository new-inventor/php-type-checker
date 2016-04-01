<?php
/**
 * User: Ionov George
 * Date: 15.02.2016
 * Time: 15:34
 */

namespace NewInventor\TypeChecker\Exception;

class ArgumentTypeException extends \Exception
{
    /**
     * ArgumentException constructor.
     * @param string $argumentName
     * @param string[] $argumentTypes
     * @param mixed $value
     * @param int $code
     * @param \Exception|null $previous
     */
    public function __construct($argumentName = '', array $argumentTypes = [], $value = '', $code = 0, $previous = null)
    {
        parent::__construct($this->getMessageString($argumentName, $argumentTypes, $value), $code, $previous);
    }

    protected function getMessageString($argumentName, $argumentTypes, $value)
    {
        $str = 'Тип аргумента';
        if(!empty($argumentName)){
            $str .= ' "' . $argumentName . '"';
        }
        $str .= ' неверен.';
        if(!empty($argumentTypes)){
            $str .= ' Ожидался тип "' . implode('" или "', $argumentTypes) . '"';
        }
        if(!empty($value)){
            $type = gettype($value);
            if($type == 'object'){
                $type = get_class($value);
            }
            $str .= ', получен "' . $type . '"';
        }

        return $str;
    }
}