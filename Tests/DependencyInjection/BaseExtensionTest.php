<?php

/*
 * This file is part of the merkDoughBundle package.
 *
 * (c) Tim Nagel <tim@nagel.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace merk\DoughBundle\Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use merk\DoughBundle\DependencyInjection\merkDoughExtension;

abstract class BaseExtensionTest extends \PHPUnit_Framework_TestCase
{
    abstract protected function loadFromFile(ContainerBuilder $container, $file);

    public function testLoadWithDefault()
    {
        $container = new ContainerBuilder();
        $extension = new merkDoughExtension();

        $extension->load(array('merk_dough' => array()), $container);

        $this->assertEquals('merk_dough.bank.default', (string)$container->getAlias('merk_dough.bank'));
    }

    public function testLoadWithMultiCurrencyOptions()
    {
        $container = $this->getContainer('multi_currency');

        $this->assertEquals(
            array(
                array('USD', 'EUR', 'RUR'),
                'USD',
                'merk_dough.exchanger'
            ),
            $container->getDefinition('merk_dough.bank')->getArguments()
        );

        $exchanger = $container->getDefinition('merk_dough.exchanger');
        $this->assertDICDefinitionMethodCallAt(0, $exchanger, 'addRate', array('USD', 'EUR', 0.774));
        $this->assertDICDefinitionMethodCallAt(1, $exchanger, 'addRate', array('USD', 'RUR', 30.179));
        $this->assertDICDefinitionMethodCallAt(2, $exchanger, 'addRate', array('EUR', 'USD', 1.292));
        $this->assertDICDefinitionMethodCallAt(3, $exchanger, 'addRate', array('EUR', 'RUR', 38.983));
    }

    protected function getContainer($file)
    {
        $container = new ContainerBuilder();
        $extension = new merkDoughExtension();
        $container->registerExtension($extension);

        $this->loadFromFile($container, $file);

        $container->compile();

        return $container;
    }

    protected function assertDICDefinitionMethodCallAt($pos, Definition $definition, $methodName, array $params = null)
    {
        $calls = $definition->getMethodCalls();
        if (isset($calls[$pos][0])) {
            $this->assertEquals($methodName, $calls[$pos][0], "Method '".$methodName."' is expected to be called at position $pos.");

            if ($params !== null) {
                $this->assertEquals($params, $calls[$pos][1], "Expected parameters to methods '".$methodName."' do not match the actual parameters.");
            }
        }
    }
}