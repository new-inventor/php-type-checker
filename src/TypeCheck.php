<?php
/**
 * Created by PhpStorm.
 * User: inventor
 * Date: 16.09.2016
 * Time: 22:59
 */

namespace NewInventor\TypeChecker;


use NewInventor\TypeChecker\Exception\ArgumentTypeException;

trait TypeCheck
{
    /**
     * @param int $paramIndex
     *
     * @return TypeChecker
     * @throws \NewInventor\TypeChecker\Exception\ArgumentTypeException
     */
    public static function param($paramIndex = 0)
    {
        if(!is_numeric($paramIndex)){
            throw new ArgumentTypeException(debug_backtrace(null, 1)[0], 0, $paramIndex, ['int']);
        }
        $paramIndex = (int)$paramIndex;
        $trace = debug_backtrace(null, 2)[1];
        $typeChecker = new TypeChecker($trace['args'][$paramIndex]);
        $typeChecker
            ->setBackTrace($trace)
            ->setIndex($paramIndex)
        ;
        
        return $typeChecker;
    }
    
    /**
     * @param $value
     *
     * @return TypeChecker
     */
    public static function variable($value)
    {
        $trace = debug_backtrace(null, 2)[1];
        $typeChecker = new TypeChecker($value);
        $typeChecker->setBackTrace($trace);
    
        return $typeChecker;
    }
}