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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\MoneyType as BaseMoneyType;
use merk\DoughBundle\Form\DataTransformer\MoneyToValueTransformer;

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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->addModelTransformer(new MoneyToValueTransformer($this->bank, $options['currency']))
            ->setAttribute('currency', $options['currency']);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'merk_dough_money';
    }
}
