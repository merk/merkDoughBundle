<?php

/*
 * This file is part of the merkDoughBundle package.
 *
 * (c) Tim Nagel <tim@nagel.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace merk\DoughBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Dough\Money\BaseMoney;

/**
 * merkDoughBundle
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class merkDoughBundle extends Bundle
{
    public function boot()
    {
        parent::boot();

        BaseMoney::setBank($this->container->get('merk_dough.bank'));
    }
}
