<?php

namespace Biplane\EnumBundle\Tests\Serializer\Handler;

use Doctrine\Common\Annotations\AnnotationReader;
use Metadata\MetadataFactory;
use JMS\SerializerBundle\Metadata\Driver\AnnotationDriver;
use JMS\SerializerBundle\Serializer\Construction\UnserializeObjectConstructor;
use JMS\SerializerBundle\Serializer\Naming\CamelCaseNamingStrategy;
use JMS\SerializerBundle\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\SerializerBundle\Serializer\JsonDeserializationVisitor;
use JMS\SerializerBundle\Serializer\XmlDeserializationVisitor;
use JMS\SerializerBundle\Serializer\JsonSerializationVisitor;
use JMS\SerializerBundle\Serializer\XmlSerializationVisitor;
use JMS\SerializerBundle\Serializer\Serializer;
use Biplane\EnumBundle\Serializer\Handler\EnumHandler;
use Biplane\EnumBundle\Tests\Fixtures\SimpleEnum;

abstract class BaseSerializationTest extends \PHPUnit_Framework_TestCase
{
    public function testEnum()
    {
        $enum = SimpleEnum::create(SimpleEnum::SECOND);

        $this->assertEquals($this->getContent('enum'), $this->serialize($enum));
    }

    public function testArrayEnums()
    {
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
        if (!class_exists('JMS\SerializerBundle\Serializer\Serializer')) {
            $this->markTestSkipped('JMSSerializerBundle is not available.');
        }
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
        $objectConstructor = new UnserializeObjectConstructor();

        $customSerializationHandlers = array(
            new EnumHandler()
        );
        $customDeserializationHandlers = array();

        $serializationVisitors = array(
            'json' => new JsonSerializationVisitor($namingStrategy, $customSerializationHandlers),
            'xml'  => new XmlSerializationVisitor($namingStrategy, $customSerializationHandlers),
        );
        $deserializationVisitors = array(
            'json' => new JsonDeserializationVisitor($namingStrategy, $customDeserializationHandlers, $objectConstructor),
            'xml'  => new XmlDeserializationVisitor($namingStrategy, $customDeserializationHandlers, $objectConstructor),
        );

        return new Serializer($factory, $serializationVisitors, $deserializationVisitors);
    }
}