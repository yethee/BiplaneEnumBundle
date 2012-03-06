<?php

namespace Biplane\EnumBundle\DependencyInjection\Factory;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use JMS\SerializerBundle\DependencyInjection\HandlerFactoryInterface;

/**
 * Factory creates definition for the enum handler.
 *
 * @author Denis Vasilev <yethee@biplane.ru>
 */
class EnumHandlerFactory implements HandlerFactoryInterface
{
    public function getConfigKey()
    {
        return 'biplane_enum';
    }

    public function addConfiguration(ArrayNodeDefinition $builder)
    {
        $builder->addDefaultsIfNotSet();
    }

    public function getType(array $config)
    {
        return self::TYPE_SERIALIZATION;
    }

    public function getHandlerId(ContainerBuilder $container, array $config)
    {
        return 'biplane_enum.jms_serializer.enum_handler';
    }
}