<?php

namespace Biplane\EnumBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

/**
 * BiplaneEnumExtension
 *
 * @author Denis Vasilev <yethee@biplane.ru>
 */
class BiplaneEnumExtension extends ConfigurableExtension
{
    /**
     * {@inheritdoc}
     */
    protected function loadInternal(array $config, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        if (count($config['serializer']['types']) > 0) {
            $definition = $container->getDefinition('biplane_enum.jms_serializer.enum_handler');
            $methods = array(
                'json' => 'serializeEnumToJson',
                'xml'  => 'serializeEnumToXml',
            );

            foreach ($config['serializer']['types'] as $type) {
                foreach ($methods as $format => $method) {
                    $definition->addTag('jms_serializer.handler', array(
                        'direction' => 'serialization',
                        'type'      => $type,
                        'format'    => $format,
                        'method'    => $method,
                    ));
                }
            }
        } else {
            $container->removeDefinition('biplane_enum.jms_serializer.enum_handler');
        }
    }
}