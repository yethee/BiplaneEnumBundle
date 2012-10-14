<?php

namespace Biplane\EnumBundle\Tests\Enumeration;

use Biplane\EnumBundle\Tests\Fixtures\FlagsEnum;
use Biplane\EnumBundle\Tests\Fixtures\FlagsWithZeroEnum;

/**
 * @author Denis Vasilev <yethee@biplane.ru>
 */
class FlaggedEnumTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowExceptionWhenValueIsNotInteger()
    {
        FlagsEnum::isAcceptableValue('1');
    }

    /**
     * @dataProvider valuesProvider
     */
    public function testAcceptableValue($value, $result)
    {
        $this->assertSame($result, FlagsEnum::isAcceptableValue($value),
            sprintf('->isAcceptableValue() returns %s if the value %d.', $result ? 'true' : 'false', $value)
        );
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testThrowExceptionWhenBitmaskIsInvalid()
    {
        $reflection = new \ReflectionMethod(
            'Biplane\\EnumBundle\\Tests\\Fixtures\\InvalidFlagsEnum',
            'getBitmask'
        );
        $reflection->setAccessible(true);

        $reflection->invoke(null);
    }

    public function testGetFlagsOfValue()
    {
        $value = FlagsWithZeroEnum::create(
            FlagsWithZeroEnum::NONE | FlagsWithZeroEnum::FIRST | FlagsWithZeroEnum::THIRD
        );

        $this->assertEquals(array(FlagsWithZeroEnum::FIRST, FlagsWithZeroEnum::THIRD), $value->getFlags());
    }

    public function testSingleFlagCanBeReadabled()
    {
        $this->assertEquals('First', FlagsEnum::getReadableFor(FlagsEnum::FIRST));
    }

    public function testMultipleFlagsCanBeReadabled()
    {
        $this->assertEquals('First; Second', FlagsEnum::getReadableFor(FlagsEnum::FIRST | FlagsEnum::SECOND));
    }

    public function testAddFlags()
    {
        $original = FlagsEnum::create(FlagsEnum::FIRST | FlagsEnum::THIRD);

        $result = $original->addFlags(FlagsEnum::SECOND | FlagsEnum::THIRD);

        $this->assertNotSame($original, $result);
        $this->assertTrue($result->hasFlag(FlagsEnum::FIRST));
        $this->assertTrue($result->hasFlag(FlagsEnum::SECOND));
        $this->assertTrue($result->hasFlag(FlagsEnum::THIRD));
    }

    /**
     * @expectedException \Biplane\EnumBundle\Exception\InvalidEnumArgumentException
     */
    public function testThrowExceptionWhenInvalidFlagsAdded()
    {
        $value = FlagsEnum::create(FlagsEnum::FIRST);

        $value->addFlags(FlagsEnum::ALL + 1);
    }

    public function testRemoveFlags()
    {
        $original = FlagsEnum::create(FlagsEnum::FIRST | FlagsEnum::THIRD);

        $result = $original->removeFlags(FlagsEnum::FIRST | FlagsEnum::FOURTH);

        $this->assertNotSame($original, $result);
        $this->assertFalse($result->hasFlag(FlagsEnum::FIRST));
        $this->assertTrue($result->hasFlag(FlagsEnum::THIRD));
        $this->assertFalse($result->hasFlag(FlagsEnum::FOURTH));
    }

    /**
     * @expectedException \Biplane\EnumBundle\Exception\InvalidEnumArgumentException
     */
    public function testThrowExceptionWhenInvalidFlagsRemoved()
    {
        $value = FlagsEnum::create(FlagsEnum::FIRST);

        $value->removeFlags(0);
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