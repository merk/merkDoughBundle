<?php

/*
 * This file is part of the merkDoughBundle package.
 *
 * (c) Tim Nagel <tim@nagel.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (file_exists($file = __DIR__ . '/../vendor/autoload.php')) {
    require_once $file;
} else {
    die(<<<'EOT'

You must set up the project dependencies, run the following commands:
curl -s http://getcomposer.org/installer | php
php composer.phar install --dev


EOT
    );
}