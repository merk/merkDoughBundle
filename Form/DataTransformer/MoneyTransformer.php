<?php

namespace merk\DoughBundle\Form\DataTransformer;

use Dough\Bank\BankInterface;
use Dough\Money\MoneyInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\DataTransformer\MoneyToLocalizedStringTransformer;

/**
 * Transforms money textfields into Money instances.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class MoneyTransformer extends MoneyToLocalizedStringTransformer
{
    /**
     * @var \Dough\Bank\BankInterface
     */
    private $bank;

    /**
     * Constructor.
     *
     * @param \Dough\Bank\BankInterface $bank
     */
    public function __construct(BankInterface $bank, $precision = null, $grouping = null, $roundingMode = null, $divisor = null)
    {
        $this->bank = $bank;

        parent::__construct($precision, $grouping, $roundingMode, $divisor);
    }

    /**
     * Transforms a Money object to its value.
     *
     * @param mixed $val
     * @return float
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException
     */
    public function transform($val)
    {
        if (null === $val) {
            return '';
        }

        if (!$val instanceof \Dough\Money\MoneyInterface) {
            throw new TransformationFailedException(sprintf('Unexpected value, expected MoneyInterface, got %s', is_object($val) ? get_class($val) : gettype($val)));
        }

        return parent::transform($val->reduce($this->bank)->getAmount());
    }

    /**
     *
     * @param mixed $val
     * @return MoneyInterface
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException
     */
    public function reverseTransform($val)
    {
        $val = parent::reverseTransform($val);

        if (!$val) {
            return null;
        }

        return $this->bank->createMoney($val);
    }
}