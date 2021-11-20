<?php

namespace Biplane\EnumBundle\Tests\Enumeration;

use Biplane\EnumBundle\Exception\InvalidEnumArgumentException;
use Biplane\EnumBundle\Tests\Fixtures\FlagsEnum;
use Biplane\EnumBundle\Tests\Fixtures\InvalidFlagsEnum;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

/**
 * @author Denis Vasilev <yethee@biplane.ru>
 */
class FlaggedEnumTest extends TestCase
{
    public function testThrowExceptionWhenValueIsNotInteger(): void
    {
        $this->expectException(InvalidArgumentException::class);

        FlagsEnum::isAcceptableValue('1');
    }

    /**
     * @dataProvider valuesProvider
     */
    public function testAcceptableValue($value, $result): void
    {
        self::assertSame($result, FlagsEnum::isAcceptableValue($value),
            sprintf('->isAcceptableValue() returns %s if the value %d.', $result ? 'true' : 'false', $value)
        );
    }

    public function testThrowExceptionWhenBitmaskIsInvalid(): void
    {
        $this->expectException(UnexpectedValueException::class);

        InvalidFlagsEnum::create(InvalidFlagsEnum::FIRST);
    }

    public function testGetFlagsOfValue(): void
    {
        $value = FlagsEnum::create(
            FlagsEnum::NONE | FlagsEnum::FIRST | FlagsEnum::THIRD
        );

        self::assertEquals(array(FlagsEnum::FIRST, FlagsEnum::THIRD), $value->getFlags());
    }

    public function testSingleFlagCanBeReadabled(): void
    {
        self::assertEquals('First', FlagsEnum::getReadableFor(FlagsEnum::FIRST));
        $instance = FlagsEnum::create(FlagsEnum::FIRST);
        self::assertEquals('First', $instance->getReadable());
    }

    public function testMultipleFlagsCanBeReadabled(): void
    {
        self::assertEquals('First; Second', FlagsEnum::getReadableFor(FlagsEnum::FIRST | FlagsEnum::SECOND));
        $instance = FlagsEnum::create(FlagsEnum::FIRST | FlagsEnum::SECOND);
        self::assertEquals('First; Second', $instance->getReadable());
    }

    public function testNoneCanBeReadabled(): void
    {
        self::assertEquals('None', FlagsEnum::getReadableFor(FlagsEnum::NONE));
        $instance = FlagsEnum::create(FlagsEnum::NONE);
        self::assertEquals('None', $instance->getReadable());
    }

    public function testReadableSeparatorCanBeChanged(): void
    {
        self::assertEquals('First | Second', FlagsEnum::getReadableFor(FlagsEnum::FIRST | FlagsEnum::SECOND, ' | '));
        $instance = FlagsEnum::create(FlagsEnum::FIRST | FlagsEnum::SECOND);
        self::assertEquals('First | Second', $instance->getReadable(' | '));
    }

    public function testAddFlags(): void
    {
        $original = FlagsEnum::create(FlagsEnum::FIRST | FlagsEnum::THIRD);

        $result = $original->addFlags(FlagsEnum::SECOND | FlagsEnum::THIRD);

        self::assertNotSame($original, $result);
        self::assertTrue($result->hasFlag(FlagsEnum::FIRST));
        self::assertTrue($result->hasFlag(FlagsEnum::SECOND));
        self::assertTrue($result->hasFlag(FlagsEnum::THIRD));
    }

    public function testThrowExceptionWhenInvalidFlagsAdded(): void
    {
        $value = FlagsEnum::create(FlagsEnum::FIRST);

        $this->expectException(InvalidEnumArgumentException::class);

        $value->addFlags(FlagsEnum::ALL + 1);
    }

    public function testRemoveFlags(): void
    {
        $original = FlagsEnum::create(FlagsEnum::FIRST | FlagsEnum::THIRD);

        $result = $original->removeFlags(FlagsEnum::FIRST | FlagsEnum::FOURTH);

        self::assertNotSame($original, $result);
        self::assertFalse($result->hasFlag(FlagsEnum::FIRST));
        self::assertTrue($result->hasFlag(FlagsEnum::THIRD));
        self::assertFalse($result->hasFlag(FlagsEnum::FOURTH));
    }

    public function testRemoveAllFlags(): void
    {
        $value = FlagsEnum::FIRST | FlagsEnum::THIRD;
        $original = FlagsEnum::create($value);

        $result = $original->removeFlags($value);

        self::assertCount(0, $result->getFlags());
        self::assertEquals(FlagsEnum::NONE, $result->getValue());
    }

    public function testThrowExceptionWhenInvalidFlagsRemoved(): void
    {
        $value = FlagsEnum::create(FlagsEnum::FIRST);

        $this->expectException(InvalidEnumArgumentException::class);

        $value->removeFlags(99);
    }

    public function valuesProvider(): array
    {
        return array(
            array(0, true),
            array(FlagsEnum::FIRST, true),
            array(3, true),
            array(8, false),
            array(10, false),
            array(23, true),
            array(55, false)
        );
    }
}
