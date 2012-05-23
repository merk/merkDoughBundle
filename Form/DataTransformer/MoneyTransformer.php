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
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Extension\Core\DataTransformer\MoneyToLocalizedStringTransformer;

/**
 * Transforms money textfields into Money instances.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class MoneyTransformer extends MoneyToLocalizedStringTransformer
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
     * @param BankInterface $bank         The bank
     * @param string|null   $currency     The currency code
     * @param int|null      $precision    Fraction digits
     * @param string|null   $grouping     Grouping separator
     * @param int|null      $roundingMode Rounding mode
     * @param float|null    $divisor      The divisor
     */
    public function __construct(BankInterface $bank, $currency = null, $precision = null, $grouping = null, $roundingMode = null, $divisor = null)
    {
        $this->bank = $bank;
        $this->currency = $currency;

        parent::__construct($precision, $grouping, $roundingMode, $divisor);
    }

    /**
     * Transforms a Money object to its value.
     *
     * @param mixed $val A Money object or null
     *
     * @return string Localized money string
     *
     * @throws UnexpectedTypeException
     * @throws TransformationFailedException
     */
    public function transform($val)
    {
        if (null === $val) {
            return '';
        }

        if (!$val instanceof MoneyInterface) {
            throw new UnexpectedTypeException($val, '\Dough\Money\MoneyInterface');
        }

        return parent::transform($this->bank->reduce($val, $this->currency)->getAmount());
    }

    /**
     * Transforms a localized money string into a Money object.
     *
     * @param string $val Localized money string
     *
     * @return MoneyInterface
     *
     * @throws TransformationFailedException
     */
    public function reverseTransform($val)
    {
        $val = parent::reverseTransform($val);

        if ($val === null) {
            return null;
        }

        return $this->bank->createMoney($val, $this->currency);
    }
}