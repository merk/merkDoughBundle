<?php

namespace merk\DoughBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilder;

class MoneyType extends AbstractType
{
    protected $moneyTransformer;

    public function __construct(DataTransformerInterface $moneyTransformer)
    {
        $this->moneyTransformer = $moneyTransformer;
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->appendClientTransformer($this->moneyTransformer);
    }

    public function getParent(array $options)
    {
        return 'text';
    }

    public function getName()
    {
        return 'dough_money';
    }
}
