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

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Config\FileLocator;

/**
 * Dependency injection extension
 */
class merkDoughExtension extends Extension
{
    /**
     * Loads the extension into the DIC.
     *
     * @param array            $configs   An array of configuration values
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $mergedConfig = $this->processConfiguration(new Configuration(), $configs);

        foreach (array('bank', 'form', 'twig') as $basename) {
            $loader->load(sprintf('%s.xml', $basename));
        }

        if (isset($mergedConfig['bank'])) {
            $container->setAlias('merk_dough.bank', 'merk_dough.bank.multi_currency');

            $container->getDefinition('merk_dough.bank.multi_currency')
                ->replaceArgument(0, $mergedConfig['bank']['currencies'])
                ->replaceArgument(1, $mergedConfig['bank']['default_currency'])
                ->replaceArgument(2, new Reference($mergedConfig['bank']['exchanger']));
        } else {
            $container->setAlias('merk_dough.bank', 'merk_dough.bank.default');
        }

        if (isset($mergedConfig['exchanger'])) {
            $definition = $container->getDefinition('merk_dough.exchanger');

            foreach ($mergedConfig['exchanger']['currencies'] as $from => $item) {
                foreach ($item['rates'] as $to => $rate) {
                    $definition->addMethodCall('addRate', array($from, $to, (float)$rate));
                }
            }
        }
    }
}
