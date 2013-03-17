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

use Doctrine\ODM\MongoDB\Mapping\Types\Type;
use Dough\Money\BaseMoney;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * merkDoughBundle
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class merkDoughBundle extends Bundle
{
    public function __construct()
    {
        Type::registerType('dough_money', 'Dough\Doctrine\ODM\MongoDB\Type\DoughMoneyType');
        Type::registerType('dough_currency_money', 'Dough\Doctrine\ODM\MongoDB\Type\DoughCurrencyMoneyType');
    }

    public function boot()
    {
        parent::boot();

        BaseMoney::setBank($this->container->get('merk_dough.bank'));
    }
}
