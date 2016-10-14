<?php
/**
 * User: Ionov George
 * Date: 15.02.2016
 * Time: 15:34
 */

namespace NewInventor\Form\TypeChecker\Exception;

use NewInventor\Form\TypeChecker\TypeChecker;

class ArgumentTypeException extends \Exception
{
    /**
     * ArgumentException constructor.
     *
     * @param TypeChecker $checker
     * @param int         $code
     * @param \Exception  $previous
     */
    public function __construct(TypeChecker $checker, $code = 0, \Exception $previous = null)
    {
        parent::__construct($checker->getErrorMessage(), $code, $previous);
    }
}