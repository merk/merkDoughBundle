<?php

/*
 * This file is part of the merkDoughBundle package.
 *
 * (c) Tim Nagel <tim@nagel.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace merk\DoughBundle\Form\Type;

use Dough\Bank\BankInterface;
use merk\DoughBundle\Form\DataTransformer\MoneyTransformer;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\MoneyType as BaseMoneyType;
use Symfony\Component\Form\FormBuilder;

class MoneyType extends BaseMoneyType
{
    protected $bank;

    /**
     * Constructor.
     *
     * @param BankInterface $bank The bank
     */
    public function __construct(BankInterface $bank)
    {
        $this->bank = $bank;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->appendClientTransformer(new MoneyTransformer(
            $this->bank,
            $options['currency'],
            $options['precision'],
            $options['grouping'],
            null,
            $options['divisor']
        ));

        $builder->setAttribute('currency', $options['currency']);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'merk_dough_money';
    }
}
