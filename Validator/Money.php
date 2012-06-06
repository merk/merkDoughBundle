<?php

/*
 * This file is part of the merkDoughBundle package.
 *
 * (c) Tim Nagel <tim@nagel.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace merk\DoughBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Constraint for MoneyInterface.
 *
 * @author Ural Davletshin <u.davletshin@biplane.ru>
 */
class Money extends Constraint
{
    public $currency;
    public $constraints = array();

    /**
     * {@inheritDoc}
     */
    public function getDefaultOption()
    {
        return 'constraints';
    }

    /**
     * {@inheritDoc}
     */
    public function getRequiredOptions()
    {
        return array('constraints');
    }

    /**
     * {@inheritDoc}
     */
    public function validatedBy()
    {
        return 'money_validator';
    }
}