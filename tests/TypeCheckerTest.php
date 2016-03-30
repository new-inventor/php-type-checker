<?php
/**
 * User: Ionov George
 * Date: 21.03.2016
 * Time: 13:44
 */
use NewInventor\TypeChecker\SimpleTypes;
use NewInventor\TypeChecker\TypeChecker;

class TypeCheckerTest extends PHPUnit_Framework_TestCase
{
    public function testCheck()
    {
        $typeChecker = TypeChecker::getInstance();
        $this->assertTrue($typeChecker->check(true, [SimpleTypes::BOOL])->result());
        $this->assertTrue($typeChecker->check('', [SimpleTypes::STRING])->result());
        $this->assertTrue($typeChecker->check(null, [SimpleTypes::NULL])->result());
        $this->assertTrue($typeChecker->check(0, [SimpleTypes::INT])->result());
        $this->assertTrue($typeChecker->check(0.10, [SimpleTypes::DOUBLE])->result());
        $this->assertTrue($typeChecker->check(0.10, [SimpleTypes::REAL])->result());
        $this->assertTrue($typeChecker->check(0.10, [SimpleTypes::FLOAT])->result());
        $this->assertTrue($typeChecker->check(new \Exception(), [SimpleTypes::OBJ])->result());
        $this->assertTrue($typeChecker->check([], [SimpleTypes::ARR])->result());
        $file = fopen(dirname(__DIR__) . '/index.php', 'r');
        $this->assertTrue($typeChecker->check($file, [SimpleTypes::RESOURCE])->result());

        $this->assertFalse($typeChecker->check('', [SimpleTypes::BOOL])->result());
        $this->assertFalse($typeChecker->check(null, [SimpleTypes::STRING])->result());
        $this->assertFalse($typeChecker->check(0, [SimpleTypes::NULL])->result());
        $this->assertFalse($typeChecker->check(false, [SimpleTypes::INT])->result());
        $this->assertFalse($typeChecker->check(new \Exception(), [SimpleTypes::FLOAT])->result());
        $this->assertFalse($typeChecker->check(0.10, [SimpleTypes::OBJ])->result());
        $this->assertFalse($typeChecker->check([], [SimpleTypes::RESOURCE])->result());
        $file = fopen(dirname(__DIR__) . '/index.php', 'r');
        $this->assertFalse($typeChecker->check($file, [SimpleTypes::ARR])->result());

        $this->assertTrue($typeChecker->check(new \Exception(), ['Exception'])->result());

        $this->assertTrue($typeChecker->check([], [SimpleTypes::ARR, SimpleTypes::OBJ])->result());
    }

    public function testCheckArray()
    {
        $typeChecker = TypeChecker::getInstance();

        $this->assertTrue($typeChecker->checkArray(['qwe', 'qwe' => 'sdasd', '12312'], [SimpleTypes::STRING])->result());
        $this->assertTrue($typeChecker->checkArray(['qwe', 'qwe', '12312' => ['sdsasdasd']], [SimpleTypes::STRING, SimpleTypes::ARR])->result());

        $this->assertFalse(TypeChecker::getInstance()->checkArray(['qwe', 'qwe', '12312' => ['sdsasdasd'], new \Exception()], [SimpleTypes::STRING, SimpleTypes::ARR])->result());
    }
}
