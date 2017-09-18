<?php

use NewInventor\TypeChecker\Exception\TypeException;
use NewInventor\TypeChecker\TypeChecker;
use Codeception\Test\Unit;
use TestsTypeChecker\TestClass;
use TestsTypeChecker\TestClass2;

class TypeCheckerTest extends Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    
    protected function _before()
    {
    }
    
    protected function _after()
    {
    }
    
    public function checkType($type, $var)
    {
        $method = 't' . $type;
        
        return TypeChecker::check($var)->$method()->result();
    }
    
    public function typeAssertions($var, array $assertions)
    {
        $this->assertSame($assertions['array'], $this->checkType('array', $var));
        $this->assertSame($assertions['bool'], $this->checkType('bool', $var));
        $this->assertSame($assertions['callable'], $this->checkType('callable', $var));
        $this->assertSame($assertions['float'], $this->checkType('float', $var));
        $this->assertSame($assertions['int'], $this->checkType('int', $var));
        $this->assertSame($assertions['null'], $this->checkType('null', $var));
        $this->assertSame($assertions['numeric'], $this->checkType('numeric', $var));
        $this->assertSame($assertions['object'], $this->checkType('object', $var));
        $this->assertSame($assertions['resource'], $this->checkType('resource', $var));
        $this->assertSame($assertions['scalar'], $this->checkType('scalar', $var));
        $this->assertSame($assertions['string'], $this->checkType('string', $var));
    }
    
    public function testSimpleTypes()
    {
        $var = ['array'];
        $this->typeAssertions(
            $var,
            [
                'array'    => true,
                'bool'     => false,
                'callable' => false,
                'float'    => false,
                'int'      => false,
                'null'     => false,
                'numeric'  => false,
                'object'   => false,
                'resource' => false,
                'scalar'   => false,
                'string'   => false,
            ]
        );
        $var = true;
        $this->typeAssertions(
            $var,
            [
                'array'    => false,
                'bool'     => true,
                'callable' => false,
                'float'    => false,
                'int'      => false,
                'null'     => false,
                'numeric'  => false,
                'object'   => false,
                'resource' => false,
                'scalar'   => true,
                'string'   => false,
            ]
        );
        $var = 2.1;
        $this->typeAssertions(
            $var,
            [
                'array'    => false,
                'bool'     => false,
                'callable' => false,
                'float'    => true,
                'int'      => false,
                'null'     => false,
                'numeric'  => true,
                'object'   => false,
                'resource' => false,
                'scalar'   => true,
                'string'   => false,
            ]
        );
        $var = 1;
        $this->typeAssertions(
            $var,
            [
                'array'    => false,
                'bool'     => false,
                'callable' => false,
                'float'    => false,
                'int'      => true,
                'null'     => false,
                'numeric'  => true,
                'object'   => false,
                'resource' => false,
                'scalar'   => true,
                'string'   => false,
            ]
        );
        $var = null;
        $this->typeAssertions(
            $var,
            [
                'array'    => false,
                'bool'     => false,
                'callable' => false,
                'float'    => false,
                'int'      => false,
                'null'     => true,
                'numeric'  => false,
                'object'   => false,
                'resource' => false,
                'scalar'   => false,
                'string'   => false,
            ]
        );
        $var = new \stdClass();
        $this->typeAssertions(
            $var,
            [
                'array'    => false,
                'bool'     => false,
                'callable' => false,
                'float'    => false,
                'int'      => false,
                'null'     => false,
                'numeric'  => false,
                'object'   => true,
                'resource' => false,
                'scalar'   => false,
                'string'   => false,
            ]
        );
        $var = fopen(__DIR__ . '/111.txt', 'rb');
        $this->typeAssertions(
            $var,
            [
                'array'    => false,
                'bool'     => false,
                'callable' => false,
                'float'    => false,
                'int'      => false,
                'null'     => false,
                'numeric'  => false,
                'object'   => false,
                'resource' => true,
                'scalar'   => false,
                'string'   => false,
            ]
        );
        fclose($var);
        $var = 'sdfadfadsf';
        $this->typeAssertions(
            $var,
            [
                'array'    => false,
                'bool'     => false,
                'callable' => false,
                'float'    => false,
                'int'      => false,
                'null'     => false,
                'numeric'  => false,
                'object'   => false,
                'resource' => false,
                'scalar'   => true,
                'string'   => true,
            ]
        );
    }
    
    public function testCallableType()
    {
        $var = [TypeChecker::class, 'tbool'];
        $this->typeAssertions(
            $var,
            [
                'array'    => true,
                'bool'     => false,
                'callable' => true,
                'float'    => false,
                'int'      => false,
                'null'     => false,
                'numeric'  => false,
                'object'   => false,
                'resource' => false,
                'scalar'   => false,
                'string'   => false,
            ]
        );
        
        $var = TypeChecker::class . '::check';
        $this->typeAssertions(
            $var,
            [
                'array'    => false,
                'bool'     => false,
                'callable' => true,
                'float'    => false,
                'int'      => false,
                'null'     => false,
                'numeric'  => false,
                'object'   => false,
                'resource' => false,
                'scalar'   => true,
                'string'   => true,
            ]
        );
        
        $var = function () {
            return 1;
        };
        $this->typeAssertions(
            $var,
            [
                'array'    => false,
                'bool'     => false,
                'callable' => true,
                'float'    => false,
                'int'      => false,
                'null'     => false,
                'numeric'  => false,
                'object'   => true,
                'resource' => false,
                'scalar'   => false,
                'string'   => false,
            ]
        );
    }
    
    public function testNumericType()
    {
        $var = 2.1;
        $this->typeAssertions(
            $var,
            [
                'array'    => false,
                'bool'     => false,
                'callable' => false,
                'float'    => true,
                'int'      => false,
                'null'     => false,
                'numeric'  => true,
                'object'   => false,
                'resource' => false,
                'scalar'   => true,
                'string'   => false,
            ]
        );
        
        $var = 1;
        $this->typeAssertions(
            $var,
            [
                'array'    => false,
                'bool'     => false,
                'callable' => false,
                'float'    => false,
                'int'      => true,
                'null'     => false,
                'numeric'  => true,
                'object'   => false,
                'resource' => false,
                'scalar'   => true,
                'string'   => false,
            ]
        );
        
        $var = '1';
        $this->typeAssertions(
            $var,
            [
                'array'    => false,
                'bool'     => false,
                'callable' => false,
                'float'    => false,
                'int'      => false,
                'null'     => false,
                'numeric'  => true,
                'object'   => false,
                'resource' => false,
                'scalar'   => true,
                'string'   => true,
            ]
        );
        
        $var = '1.1';
        $this->typeAssertions(
            $var,
            [
                'array'    => false,
                'bool'     => false,
                'callable' => false,
                'float'    => false,
                'int'      => false,
                'null'     => false,
                'numeric'  => true,
                'object'   => false,
                'resource' => false,
                'scalar'   => true,
                'string'   => true,
            ]
        );
        
        $var = '0';
        $this->typeAssertions(
            $var,
            [
                'array'    => false,
                'bool'     => false,
                'callable' => false,
                'float'    => false,
                'int'      => false,
                'null'     => false,
                'numeric'  => true,
                'object'   => false,
                'resource' => false,
                'scalar'   => true,
                'string'   => true,
            ]
        );
        
        $var = '26317823hfghdfghdfghdfgh';
        $this->typeAssertions(
            $var,
            [
                'array'    => false,
                'bool'     => false,
                'callable' => false,
                'float'    => false,
                'int'      => false,
                'null'     => false,
                'numeric'  => false,
                'object'   => false,
                'resource' => false,
                'scalar'   => true,
                'string'   => true,
            ]
        );
    }
    
    public function testTypes()
    {
        $var = new TestClass();
        $this->typeAssertions(
            $var,
            [
                'array'    => false,
                'bool'     => false,
                'callable' => false,
                'float'    => false,
                'int'      => false,
                'null'     => false,
                'numeric'  => false,
                'object'   => true,
                'resource' => false,
                'scalar'   => false,
                'string'   => false,
            ]
        );
    
        $this->assertTrue(TypeChecker::check($var)->types(TestClass::class)->result());
        $this->assertTrue(TypeChecker::check($var)->types('\TestsTypeChecker\TestClass')->result());
        $this->assertFalse(TypeChecker::check($var)->types()->result());
    }
    
    public function testInner()
    {
        $var = ['string', 'asdsdf', 'sdfsdf'];
        $this->assertTrue(TypeChecker::check($var)->inner()->tstring()->result());
        $var = [1, 1.2, '123'];
        $this->assertTrue(TypeChecker::check($var)->inner()->tnumeric()->result());
        $var = [new TestClass(), new TestClass(), new TestClass2()];
        $this->assertTrue(
            TypeChecker::check($var)->inner()->types(TestClass::class, TestClass2::class)->result()
        );
        $var = [new TestClass(), new TestClass(), ''];
        $this->assertFalse(
            TypeChecker::check($var)->inner()->types(TestClass::class, TestClass2::class)->result()
        );
        $var = 'a';
        $this->assertTrue(
            TypeChecker::check($var)->tstring()->inner()->types(TestClass::class, TestClass2::class)->result()
        );
        $var = '';
        $this->expectException(TypeException::class);
        TypeChecker::check($var)->fail();
    }
    
    public function testCallback()
    {
        $var = 'string';
        $this->assertTrue(
            TypeChecker::check($var)->callback(
                function ($value) {
                    return strlen($value) > 4;
                }
            )->result()
        );
        $var = '';
        $this->assertTrue(
            TypeChecker::check($var)->callback(
                function ($value) {
                    return strlen($value) < 6;
                }
            )->result()
        );
        $var = ['string', 'asdsdf', 'asdads'];
        $this->assertTrue(
            TypeChecker::check($var)->inner()->callback(
                function ($value) {
                    return strlen($value) === 6;
                }
            )->result()
        );
        $var = ['', 'asdsdf'];
        $this->assertFalse(
            TypeChecker::check($var)->inner()->callback(
                function ($value) {
                    return strlen($value) === 6;
                }
            )->result()
        );
        $var = ['asdsdf', 2];
        $this->assertFalse(
            TypeChecker::check($var)->inner()->callback(
                function ($value) {
                    return strlen($value) === 6;
                }
            )->result()
        );
    }
    
    public function testCustom()
    {
        $value = 'sdfjhskjdfadsf';
        $this->assertTrue(
            TypeChecker::check($value)->tnull()->tscalar()->callback(
                function ($value) {
                    return is_object($value) && method_exists($value, '__toString');
                }
            )->inner()->tnull()->tscalar()->callback(
                function ($value) {
                    return is_object($value) && method_exists($value, '__toString');
                }
            )->result()
        );
        $value = [null, 'ksdfhsdf', 12, 12.5];
        $this->assertTrue(
            TypeChecker::check($value)->tnull()->tscalar()->callback(
                function ($value) {
                    return is_object($value) && method_exists($value, '__toString');
                }
            )->inner()->tnull()->tscalar()->callback(
                function ($value) {
                    return is_object($value) && method_exists($value, '__toString');
                }
            )->result()
        );
    }
    
    public function testFail()
    {
        $var = ['string', 'asdsdf', 'sdfsdf'];
        TypeChecker::check($var)->inner()->tstring()->fail();
        $this->expectException(TypeException::class);
        $var = [1, 1.2, '123'];
        TypeChecker::check($var)->inner()->tstring()->fail();
    }
    
    public function testFail1()
    {
        $var = [1, 1.2, '123'];
        try {
            TypeChecker::check($var)->inner()->tstring()->tnull()->fail();
        } catch (TypeException $e) {
            $this->assertNotFalse(strpos($e->getMessage(), 'Required elements types are: string, null'));
            $this->assertNotFalse(strpos($e->getMessage(), 'Type received: integer'));
        }
    }
    
    public function testFail2()
    {
        $var = 'string';
        TypeChecker::check($var)->tstring()->fail();
        $this->expectException(TypeException::class);
        $var = 1;
        TypeChecker::check($var)->tstring()->fail();
    }
}