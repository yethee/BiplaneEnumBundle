<?php

namespace Biplane\EnumBundle\Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Biplane\EnumBundle\DependencyInjection\BiplaneEnumExtension;

class BiplaneEnumExtensionTest extends \PHPUnit_Framework_TestCase
{
    private $container;
    private $extension;

    public function testLoadWithDefaultConfig()
    {
        $this->extension->load(array(), $this->container);

        $this->assertTrue($this->container->hasDefinition('biplane_enum.form_type.enum'));
        $this->assertFalse($this->container->hasDefinition('biplane_enum.serializer.enum_handler'));
    }

    public function testEnableJmsSerializer()
    {
        $config = array(
            'biplane_enum' => array('jms_serializer' => true)
        );
        $this->extension->load($config, $this->container);

        $this->assertTrue($this->container->hasDefinition('biplane_enum.serializer.enum_handler'));
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
}