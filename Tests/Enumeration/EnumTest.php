<?php

namespace Biplane\EnumBundle\Tests\Enumeration;

use Biplane\EnumBundle\Enumeration\Enum;
use Biplane\EnumBundle\Tests\Fixtures\SimpleEnum;
use Biplane\EnumBundle\Tests\Fixtures\ExtendedSimpleEnum;

/**
 * @author Denis Vasilev <yethee@biplane.ru>
 */
class EnumTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider enumValuesProvider
     */
    public function testCreateEnumValue($value, $readableValue)
    {
        $enumValue = SimpleEnum::create($value);

        $this->assertEquals($value, $enumValue->getValue());
        $this->assertEquals($readableValue, $enumValue->getReadable());
    }

    public function testEnumToString()
    {
        $enumValue = SimpleEnum::create(SimpleEnum::FIRST);

        $this->assertEquals('First', (string)$enumValue);
    }

    /**
     * @expectedException \Biplane\EnumBundle\Exception\InvalidEnumArgumentException
     */
    public function testExceptionIsRaisedWhenValueIsNotAcceptable()
    {
        SimpleEnum::create(3);
    }

    /**
     * @expectedException \Biplane\EnumBundle\Exception\InvalidEnumArgumentException
     */
    public function testExceptionIsRaisedWhenValueIsNotAcceptableWithStrictCheck()
    {
        SimpleEnum::create('0');
    }

    public function testValueCanBeReadabled()
    {
        $this->assertEquals('Second', SimpleEnum::getReadableFor(2));
    }

    /**
     * @expectedException \Biplane\EnumBundle\Exception\InvalidEnumArgumentException
     */
    public function testExceptionIsRaisedWhenValueCannotBeReadable()
    {
        SimpleEnum::getReadableFor(3);
    }

    public function testEnumsForEqualsWithSameClass()
    {
        $enum = SimpleEnum::create(SimpleEnum::FIRST);

        $this->assertTrue($enum->equals(SimpleEnum::create(SimpleEnum::FIRST)));
        $this->assertFalse($enum->equals(SimpleEnum::create(SimpleEnum::SECOND)));
    }

    public function testEnumsForEqualsWithExtendedClasses()
    {
        $enum = SimpleEnum::create(SimpleEnum::FIRST);

        $this->assertFalse($enum->equals(ExtendedSimpleEnum::create(ExtendedSimpleEnum::FIRST)));
    }

    public function enumValuesProvider()
    {
        return array(
            array(1, 'First'),
            array(SimpleEnum::SECOND, 'Second')
        );
    }
}