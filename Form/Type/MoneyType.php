<?php

namespace merk\DoughBundle\Form\Type;

use Dough\Bank\BankInterface;
use merk\DoughBundle\Form\DataTransformer\MoneyTransformer;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\MoneyType as BaseMoneyType;
use Symfony\Component\Form\FormBuilder;

class MoneyType extends BaseMoneyType
{
    protected $bank;

    public function __construct(BankInterface $bank)
    {
        $this->bank = $bank;
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->appendClientTransformer(new MoneyTransformer(
            $this->bank,
            $options['precision'],
            $options['grouping'],
            null,
            $options['divisor']
        ));

        $builder->setAttribute('currency', $options['currency']);
    }


    public function getName()
    {
        return 'dough_money';
    }
}
