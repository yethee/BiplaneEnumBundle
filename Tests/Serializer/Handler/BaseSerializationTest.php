<?php

namespace Biplane\EnumBundle\Tests\Serializer\Handler;

use Biplane\EnumBundle\Serializer\Handler\EnumHandler;
use Biplane\EnumBundle\Tests\Fixtures\SimpleEnum;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;

abstract class BaseSerializationTest extends TestCase
{
    /**
     * @var HandlerRegistry
     */
    protected $handlerRegistry;

    /**
     * @var EnumHandler
     */
    protected $handler;

    public function testEnum(): void
    {
        $enum = SimpleEnum::create(SimpleEnum::SECOND);

        $this->registerHandler(get_class($enum));

        self::assertEquals($this->getContent('enum'), $this->serialize($enum));
    }

    public function testArrayEnums(): void
    {
        $this->registerHandler(SimpleEnum::class);

        $data = array(
            SimpleEnum::create(SimpleEnum::FIRST),
            SimpleEnum::create(SimpleEnum::SECOND)
        );

        self::assertEquals($this->getContent('array_enums'), $this->serialize($data));
    }

    abstract protected function getContent($key);
    abstract protected function getFormat(): string;

    protected function setUp(): void
    {
        if (!class_exists(Serializer::class)) {
            $this->markTestSkipped('JMSSerializer library is not available.');
        }

        $this->handlerRegistry = new HandlerRegistry();
        $this->handler = new EnumHandler();
    }

    protected function tearDown(): void
    {
        unset($this->handlerRegistry, $this->handler);
    }

    protected function registerHandler($type): void
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

    protected function getSerializer(): Serializer
    {
        return SerializerBuilder::create($this->handlerRegistry)->build();
    }
}
