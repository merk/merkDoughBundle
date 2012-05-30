<?php

/*
 * This file is part of the merkDoughBundle package.
 *
 * (c) Tim Nagel <tim@nagel.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace merk\DoughBundle\Form\DataTransformer;

use Dough\Bank\BankInterface;
use Dough\Money\MoneyInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

/**
 * Transforms between an Money instance and a raw value.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class MoneyToValueTransformer implements DataTransformerInterface
{
    /**
     * @var BankInterface
     */
    private $bank;

    /**
     * @var string|null
     */
    private $currency;

    /**
     * Constructor.
     *
     * @param BankInterface $bank     The bank
     * @param string|null   $currency The currency code
     */
    public function __construct(BankInterface $bank, $currency = null)
    {
        $this->bank = $bank;
        $this->currency = $currency;
    }

    /**
     * Transforms a Money object to its value.
     *
     * @param mixed $val A Money object or null
     *
     * @return float A scalar value
     *
     * @throws UnexpectedTypeException
     */
    public function transform($val)
    {
        if (null === $val) {
            return null;
        }

        if (!$val instanceof MoneyInterface) {
            throw new UnexpectedTypeException($val, '\Dough\Money\MoneyInterface');
        }

        return $this->bank->reduce($val, $this->currency)->getAmount();
    }

    /**
     * Transforms a scalar value to a Money object.
     *
     * @param mixed $val The value in the transformed representation
     *
     * @return MoneyInterface
     *
     * @throws UnexpectedTypeException
     */
    public function reverseTransform($val)
    {
        if ($val === null) {
            return null;
        }

        if (null !== $val && !(is_int($val) || is_float($val))) {
            throw new UnexpectedTypeException($val, 'integer or float');
        }

        return $this->bank->createMoney($val, $this->currency);
    }
}