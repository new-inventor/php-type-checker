<?php
/**
 * Created by PhpStorm.
 * User: inventor
 * Date: 16.09.2016
 * Time: 22:59
 */

namespace NewInventor\TypeChecker;


trait TypeCheck
{
    public static function param($paramIndex = 0)
    {
        return new TypeChecker(debug_backtrace(null)[1], $paramIndex);
    }
}