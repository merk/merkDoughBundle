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

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class YamlExtensionTest extends BaseExtensionTest
{
    protected function loadFromFile(ContainerBuilder $container, $file)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/Fixtures'));
        $loader->load($file.'.yml');
    }
}