<?php

namespace Biplane\EnumBundle\Tests\Serializer\Normalizer;

use Biplane\EnumBundle\Enumeration\EnumInterface;
use Biplane\EnumBundle\Serializer\Normalizer\EnumNormalizer;
use Biplane\EnumBundle\Tests\Fixtures\SimpleEnum;
use PHPUnit\Framework\TestCase;

/**
 * @author Denis Vasilev <yethee@biplane.ru>
 */
class EnumNormalizerTest extends TestCase
{
    public function testNormalize(): void
    {
        $normalizer = new EnumNormalizer();
        $value = SimpleEnum::create(SimpleEnum::FIRST);

        self::assertSame($value->getValue(), $normalizer->normalize($value));
    }

    public function testDenormalize():  void
    {
        $normalizer = new EnumNormalizer();

        $result = $normalizer->denormalize(1, SimpleEnum::class);

        self::assertInstanceOf(SimpleEnum::class, $result);
        self::assertSame(1, $result->getValue());
    }

    public function testSupportsNormalization(): void
    {
        $normalizer = new EnumNormalizer();

        self::assertTrue($normalizer->supportsNormalization(SimpleEnum::create(SimpleEnum::FIRST)));
        self::assertFalse($normalizer->supportsNormalization(null));
    }

    public function testSupportsDenormalization(): void
    {
        $normalizer = new EnumNormalizer();

        self::assertTrue($normalizer->supportsDenormalization(1, SimpleEnum::class));
        self::assertFalse($normalizer->supportsDenormalization('1', SimpleEnum::class));
        self::assertFalse($normalizer->supportsDenormalization(null, __CLASS__));
    }
}
