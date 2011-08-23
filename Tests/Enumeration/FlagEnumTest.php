<?php

namespace Biplane\EnumBundle\Tests\Enumeration;

use Biplane\EnumBundle\Tests\Fixtures\FlagEnum;

/**
 * @author Denis Vasilev <yethee@biplane.ru>
 */
class FlagEnumTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExceptionIsRaisedWhenInvalidTypeOfAcceptableValue()
    {
        FlagEnum::isAcceptableValue('1');
    }

    /**
     * @dataProvider valuesProvider
     */
    public function testAcceptableValue($value, $result)
    {
        $this->assertSame($result, FlagEnum::isAcceptableValue($value),
            sprintf('->isAcceptableValue() returns %s if the value %d.', $result ? 'true' : 'false', $value));
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testExceptionIsRaisedWhenPossibleValueIsNotBitFlag()
    {
        $reflection = new \ReflectionMethod(
            'Biplane\\EnumBundle\\Tests\\Fixtures\\InvalidFlagEnum',
            'getAllFlagsValue'
        );
        $reflection->setAccessible(true);
        
        $reflection->invoke(null);
    }

    public function testGetFlagsOfValue()
    {
        $value = FlagEnum::create(FlagEnum::FIRST | FlagEnum::THIRD);

        $this->assertEquals(array(FlagEnum::FIRST, FlagEnum::THIRD), $value->getFlags());
    }

    public function testSingleFlagCanBeReadabled()
    {
        $this->assertEquals('First', FlagEnum::getReadableFor(FlagEnum::FIRST));
    }

    public function testMultipleFlagsCanBeReadabled()
    {
        $this->assertEquals('First; Second', FlagEnum::getReadableFor(FlagEnum::FIRST | FlagEnum::SECOND));
    }

    public function valuesProvider()
    {
        return array(
            array(0, false),
            array(FlagEnum::FIRST, true),
            array(3, true),
            array(8, false),
            array(10, false),
            array(23, true),
            array(55, false)
        );
    }
}