<?php

namespace Biplane\EnumBundle\Tests\Serializer\Handler;

use Doctrine\Common\Annotations\AnnotationReader;
use Metadata\MetadataFactory;
use JMS\Serializer\Metadata\Driver\AnnotationDriver;
use JMS\Serializer\Construction\UnserializeObjectConstructor;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\Naming\CamelCaseNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\XmlSerializationVisitor;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Serializer;
use PhpCollection\Map;
use Biplane\EnumBundle\Serializer\Handler\EnumHandler;
use Biplane\EnumBundle\Tests\Fixtures\SimpleEnum;

abstract class BaseSerializationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var HandlerRegistry
     */
    protected $handlerRegistry;

    /**
     * @var EnumHandler
     */
    protected $handler;

    public function testEnum()
    {
        $enum = SimpleEnum::create(SimpleEnum::SECOND);

        $this->registerHandler(get_class($enum));

        $this->assertEquals($this->getContent('enum'), $this->serialize($enum));
    }

    public function testArrayEnums()
    {
        $this->registerHandler(SimpleEnum::class);

        $data = array(
            SimpleEnum::create(SimpleEnum::FIRST),
            SimpleEnum::create(SimpleEnum::SECOND)
        );

        $this->assertEquals($this->getContent('array_enums'), $this->serialize($data));
    }

    abstract protected function getContent($key);
    abstract protected function getFormat();

    protected function setUp()
    {
        if (!class_exists('JMS\Serializer\Serializer')) {
            $this->markTestSkipped('JMSSerializer library is not available.');
        }

        $this->handlerRegistry = new HandlerRegistry();
        $this->handler = new EnumHandler();
    }

    protected function tearDown()
    {
        unset($this->handlerRegistry, $this->handler);
    }

    protected function registerHandler($type)
    {
        $this->handlerRegistry->registerHandler(
            GraphNavigator::DIRECTION_SERIALIZATION,
            $type,
            $this->getFormat(),
            array($this->handler, 'serializeEnumTo' . ucfirst($this->getFormat()))
        );
    }

    protected function serialize($data)
    {
        return $this->getSerializer()->serialize($data, $this->getFormat());
    }

    protected function deserialize($content, $type)
    {
        return $this->getSerializer()->deserialize($content, $type, $this->getFormat());
    }

    protected function getSerializer()
    {
        $factory = new MetadataFactory(new AnnotationDriver(new AnnotationReader()));
        $namingStrategy = new SerializedNameAnnotationStrategy(new CamelCaseNamingStrategy());

        $serializationVisitors = new Map(array(
            'json' => new JsonSerializationVisitor($namingStrategy),
            'xml'  => new XmlSerializationVisitor($namingStrategy),
        ));

        return new Serializer(
            $factory,
            $this->handlerRegistry,
            new UnserializeObjectConstructor(),
            $serializationVisitors,
            new Map()
        );
    }
}