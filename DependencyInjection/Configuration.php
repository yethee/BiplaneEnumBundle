<?php

namespace Biplane\EnumBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration
 *
 * @author Denis Vasilev <yethee@biplane.ru>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('biplane_enum');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('serializer')
                    ->addDefaultsIfNotSet()
                    ->fixXmlConfig('type')
                    ->children()
                        ->arrayNode('types')
                            ->useAttributeAsKey('name')
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
