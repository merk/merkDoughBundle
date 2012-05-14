<?php

/*
 * This file is part of the merkDoughBundle package.
 *
 * (c) Tim Nagel <tim@nagel.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace merk\DoughBundle\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * Configuration
 *
 * @author Denis Vasilev <liquid.yethee@hotmail.com>
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

        $treeBuilder->root('merk_dough')
            ->children()
                ->arrayNode('bank')
                    ->fixXmlConfig('currency', 'currencies')
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('default_currency')->isRequired()->end()
                        ->arrayNode('currencies')
                            ->isRequired()
                            ->requiresAtLeastOneElement()
                            ->prototype('scalar')->end()
                        ->end()
                        ->scalarNode('exchanger')->defaultValue('merk_dough.exchanger')->end()
                    ->end()
                ->end()
                ->arrayNode('exchanger')
                    ->fixXmlConfig('currency', 'currencies')
                    ->children()
                        ->arrayNode('currencies')
                            ->requiresAtLeastOneElement()
                            ->useAttributeAsKey('name')
                            ->prototype('array')
                                ->fixXmlConfig('rate')
                                ->children()
                                    ->arrayNode('rates')
                                        ->useAttributeAsKey('name')
                                        ->prototype('scalar')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}