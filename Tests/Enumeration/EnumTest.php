<?php

namespace Biplane\EnumBundle\Tests\Enumeration;

use Biplane\EnumBundle\Enumeration\Enum;
use Biplane\EnumBundle\Exception\InvalidEnumArgumentException;
use Biplane\EnumBundle\Tests\Fixtures\SimpleEnum;
use Biplane\EnumBundle\Tests\Fixtures\ExtendedSimpleEnum;
use PHPUnit\Framework\TestCase;

/**
 * @author Denis Vasilev <yethee@biplane.ru>
 */
class EnumTest extends TestCase
{
    /**
     * @dataProvider enumValuesProvider
     */
    public function testCreateEnumValue($value, $readableValue): void
    {
        $enumValue = SimpleEnum::create($value);

        self::assertEquals($value, $enumValue->getValue());
        self::assertEquals($readableValue, $enumValue->getReadable());
    }

    public function testEnumToString(): void
    {
        $enumValue = SimpleEnum::create(SimpleEnum::FIRST);

        self::assertEquals('First', (string)$enumValue);
    }

    public function testExceptionIsRaisedWhenValueIsNotAcceptable(): void
    {
        $this->expectException(InvalidEnumArgumentException::class);

        SimpleEnum::create(3);
    }

    public function testExceptionIsRaisedWhenValueIsNotAcceptableWithStrictCheck(): void
    {
        $this->expectException(InvalidEnumArgumentException::class);

        SimpleEnum::create('0');
    }

    public function testValueCanBeReadabled(): void
    {
        self::assertEquals('Second', SimpleEnum::getReadableFor(2));
    }

    public function testExceptionIsRaisedWhenValueCannotBeReadable(): void
    {
        $this->expectException(InvalidEnumArgumentException::class);

        SimpleEnum::getReadableFor(3);
    }

    public function testEnumsForEqualsWithSameClass(): void
    {
        $enum = SimpleEnum::create(SimpleEnum::FIRST);

        self::assertTrue($enum->equals(SimpleEnum::create(SimpleEnum::FIRST)));
        self::assertFalse($enum->equals(SimpleEnum::create(SimpleEnum::SECOND)));
    }

    public function testEnumsForEqualsWithExtendedClasses(): void
    {
        $enum = SimpleEnum::create(SimpleEnum::FIRST);

        self::assertFalse($enum->equals(ExtendedSimpleEnum::create(ExtendedSimpleEnum::FIRST)));
    }

    public function enumValuesProvider(): array
    {
        return array(
            array(1, 'First'),
            array(SimpleEnum::SECOND, 'Second')
        );
    }
}
