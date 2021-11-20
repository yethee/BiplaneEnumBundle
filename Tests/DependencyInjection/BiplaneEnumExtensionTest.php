<?php

namespace Biplane\EnumBundle\Tests\DependencyInjection;

use Biplane\EnumBundle\DependencyInjection\BiplaneEnumExtension;
use Biplane\EnumBundle\Tests\Fixtures\FlagsEnum;
use Biplane\EnumBundle\Tests\Fixtures\SimpleEnum;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Denis Vasilev <yethee@biplane.ru>
 */
class BiplaneEnumExtensionTest extends TestCase
{
    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * @var BiplaneEnumExtension
     */
    private $extension;

    public function testLoadWithDefaults(): void
    {
        $this->extension->load(array(), $this->container);

        self::assertFalse($this->container->hasDefinition('biplane_enum.jms_serializer.enum_handler'));
    }

    public function testLoadSerializationTypes(): void
    {
        $config = array(
            'serializer' => array(
                'types' => array(
                    SimpleEnum::class,
                    FlagsEnum::class,
                )
            )
        );

        $this->extension->load(array('biplane_enum' => $config), $this->container);

        self::assertTrue($this->container->hasDefinition('biplane_enum.jms_serializer.enum_handler'));

        $tagAttributes = $this->container->getDefinition('biplane_enum.jms_serializer.enum_handler')
            ->getTag('jms_serializer.handler');

        self::assertCount(4, $tagAttributes);
        self::assertTagAttributes($tagAttributes[0], SimpleEnum::class, 'json');
        self::assertTagAttributes($tagAttributes[1], SimpleEnum::class, 'xml');
        self::assertTagAttributes($tagAttributes[2], FlagsEnum::class, 'json');
        self::assertTagAttributes($tagAttributes[3], FlagsEnum::class, 'xml');
    }

    protected function setUp(): void
    {
        $this->container = new ContainerBuilder();
        $this->extension = new BiplaneEnumExtension();
    }

    protected function tearDown(): void
    {
        unset($this->container, $this->extension);
    }

    private static function assertTagAttributes(array $attributes, $type, $format): void
    {
        $expected = array(
            'direction' => 'serialization',
            'type'      => $type,
            'format'    => $format,
            'method'    => 'serializeEnumTo' . ucfirst($format),
        );

        self::assertEquals($expected, $attributes);
    }
}
