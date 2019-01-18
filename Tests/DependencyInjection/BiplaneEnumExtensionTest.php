<?php

namespace Biplane\EnumBundle\Tests\DependencyInjection;

use Biplane\EnumBundle\DependencyInjection\BiplaneEnumExtension;
use Biplane\EnumBundle\Tests\Fixtures\FlagsEnum;
use Biplane\EnumBundle\Tests\Fixtures\SimpleEnum;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Denis Vasilev <yethee@biplane.ru>
 */
class BiplaneEnumExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * @var BiplaneEnumExtension
     */
    private $extension;

    public function testLoadWithDefaults()
    {
        $this->extension->load(array(), $this->container);

        $this->assertFalse($this->container->hasDefinition('biplane_enum.jms_serializer.enum_handler'));
    }

    public function testLoadSerializationTypes()
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

        $this->assertTrue($this->container->hasDefinition('biplane_enum.jms_serializer.enum_handler'));

        $tagAttributes = $this->container->getDefinition('biplane_enum.jms_serializer.enum_handler')
            ->getTag('jms_serializer.handler');

        $this->assertCount(4, $tagAttributes);
        $this->assertTagAttributes($tagAttributes[0], SimpleEnum::class, 'json');
        $this->assertTagAttributes($tagAttributes[1], SimpleEnum::class, 'xml');
        $this->assertTagAttributes($tagAttributes[2], FlagsEnum::class, 'json');
        $this->assertTagAttributes($tagAttributes[3], FlagsEnum::class, 'xml');
    }

    protected function setUp()
    {
        $this->container = new ContainerBuilder();
        $this->extension = new BiplaneEnumExtension();
    }

    protected function tearDown()
    {
        unset($this->container, $this->extension);
    }

    private function assertTagAttributes(array $attributes, $type, $format)
    {
        $expected = array(
            'direction' => 'serialization',
            'type'      => $type,
            'format'    => $format,
            'method'    => 'serializeEnumTo' . ucfirst($format),
        );

        $this->assertEquals($expected, $attributes);
    }
}