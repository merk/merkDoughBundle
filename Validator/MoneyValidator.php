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
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Dough\Bank\BankInterface;
use Dough\Money\Money as DoughMoney;

/**
 * Validator for Money.
 *
 * @author Ural Davletshin <u.davletshin@biplane.ru>
 */
class MoneyValidator extends ConstraintValidator
{
    /**
     * @var BankInterface
     */
    private $bank;

    /**
     * Class constructor.
     *
     * @param BankInterface $bank A bank instance
     */
    public function __construct(BankInterface $bank)
    {
        $this->bank = $bank;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed      $value      The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     *
     * @api
     */
    public function validate($value, Constraint $constraint)
    {
        /** @var $value DoughMoney */
        if (null === $value) {
            return;
        }

        if (!$value instanceof DoughMoney) {
            throw new UnexpectedTypeException($value, 'Dough\Money\Money');
        }

        $walker = $this->context->getGraphWalker();
        $group = $this->context->getGroup();
        $propertyPath = $this->context->getPropertyPath();
        $constraints = !is_array($constraint->constraints) ? array($constraint->constraints) : $constraint->constraints;

        if (null !== $constraint->currency) {
            $value = $this->bank->reduce($value, $constraint->currency);
        }

        foreach ($constraints as $constr) {
            $walker->walkConstraint($constr, $value->getAmount(), $group, $propertyPath);
        }
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed      $value      The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     *
     * @return Boolean Whether or not the value is valid
     *
     * @api
     */
    public function isValid($value, Constraint $constraint)
    {
        $this->validate($value, $constraint);

        return true;
    }
}