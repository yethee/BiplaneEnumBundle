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
     * Generates the configuration tree builder.
     *
     * @return TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $treeBuilder->root('biplane_enum', 'array')
            ->children()
                ->scalarNode('jms_serializer')->defaultFalse()->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}