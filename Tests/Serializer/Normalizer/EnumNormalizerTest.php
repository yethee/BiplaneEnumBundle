<?php

namespace Biplane\EnumBundle\Tests\Serializer\Normalizer;

use Biplane\EnumBundle\Enumeration\EnumInterface;
use Biplane\EnumBundle\Serializer\Normalizer\EnumNormalizer;
use Biplane\EnumBundle\Tests\Fixtures\SimpleEnum;

/**
 * @author Denis Vasilev <yethee@biplane.ru>
 */
class EnumNormalizerTest extends \PHPUnit_Framework_TestCase
{
    public function testNormalize()
    {
        $rawEnumValue = 5;

        $enum = $this->getMock(EnumInterface::class);
        $enum->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue($rawEnumValue));

        $normalizer = new EnumNormalizer();

        $this->assertEquals($rawEnumValue, $normalizer->normalize($enum));
    }

    public function testDenormalize()
    {
        $normalizer = new EnumNormalizer();

        $result = $normalizer->denormalize(1, SimpleEnum::class);

        $this->assertInstanceOf(SimpleEnum::class, $result);
        $this->assertEquals(1, $result->getValue());
    }

    public function testSupportsNormalization()
    {
        $normalizer = new EnumNormalizer();
        $enum = $this->getMock(EnumInterface::class);

        $this->assertTrue($normalizer->supportsNormalization($enum));
        $this->assertFalse($normalizer->supportsNormalization(null));
    }

    public function testSupportsDenormalization()
    {
        $normalizer = new EnumNormalizer();

        $this->assertTrue($normalizer->supportsDenormalization(1, SimpleEnum::class));
        $this->assertFalse($normalizer->supportsDenormalization('1', SimpleEnum::class));
        $this->assertFalse($normalizer->supportsDenormalization(null, __CLASS__));
    }
}