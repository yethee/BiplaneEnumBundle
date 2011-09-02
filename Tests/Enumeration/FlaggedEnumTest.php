<?php

namespace Biplane\EnumBundle\Tests\Enumeration;

use Biplane\EnumBundle\Tests\Fixtures\FlagsEnum;

/**
 * @author Denis Vasilev <yethee@biplane.ru>
 */
class FlaggedEnumTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExceptionIsRaisedWhenInvalidTypeOfAcceptableValue()
    {
        FlagsEnum::isAcceptableValue('1');
    }

    /**
     * @dataProvider valuesProvider
     */
    public function testAcceptableValue($value, $result)
    {
        $this->assertSame($result, FlagsEnum::isAcceptableValue($value),
            sprintf('->isAcceptableValue() returns %s if the value %d.', $result ? 'true' : 'false', $value));
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testExceptionIsRaisedWhenPossibleValueIsNotBitFlag()
    {
        $reflection = new \ReflectionMethod(
            'Biplane\\EnumBundle\\Tests\\Fixtures\\InvalidFlagsEnum',
            'getAllFlagsValue'
        );
        $reflection->setAccessible(true);
        
        $reflection->invoke(null);
    }

    public function testGetFlagsOfValue()
    {
        $value = FlagsEnum::create(FlagsEnum::FIRST | FlagsEnum::THIRD);

        $this->assertEquals(array(FlagsEnum::FIRST, FlagsEnum::THIRD), $value->getFlags());
    }

    public function testSingleFlagCanBeReadabled()
    {
        $this->assertEquals('First', FlagsEnum::getReadableFor(FlagsEnum::FIRST));
    }

    public function testMultipleFlagsCanBeReadabled()
    {
        $this->assertEquals('First; Second', FlagsEnum::getReadableFor(FlagsEnum::FIRST | FlagsEnum::SECOND));
    }

    public function valuesProvider()
    {
        return array(
            array(0, false),
            array(FlagsEnum::FIRST, true),
            array(3, true),
            array(8, false),
            array(10, false),
            array(23, true),
            array(55, false)
        );
    }
}