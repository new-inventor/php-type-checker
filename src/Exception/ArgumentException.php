<?php
/**
 * User: Ionov George
 * Date: 15.02.2016
 * Time: 16:46
 */

namespace NewInventor\TypeChecker\Exception;

class ArgumentException extends \Exception
{
    /**
     * ArgumentException constructor.
     * @param string $message
     * @param string $argumentName
     * @param int $code
     * @param \Exception|null $previous
     */
    public function __construct($message = '', $argumentName = '', $code = 0, $previous = null)
    {
        parent::__construct($this->getMessageString($message, $argumentName), $code, $previous);
    }

    protected function getMessageString($message, $argumentName)
    {
        $str = 'Ошибка при обработке аргумента';
        if(!empty($argumentName)){
            $str .= ' "' . $argumentName . '"';
        }
        if(!empty($message)){
            $str .= ': ' . $message;
        }

        return $str;
    }
}