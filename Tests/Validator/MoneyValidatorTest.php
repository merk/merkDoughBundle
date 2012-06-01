<?php

namespace merk\DoughBundle\Tests\Validator;

use merk\DoughBundle\Validator\Money as MoneyConstraint;
use merk\DoughBundle\Validator\MoneyValidator;
use Dough\Money\Money;
use Symfony\Component\Validator\Constraints\Min;
use Symfony\Component\Validator\Constraints\Max;


/**
 * Test for MoneyValidator.
 *
 * @author Ural Davletshin <u.davletshin@biplane.ru>
 */
class MoneyValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $walker;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $context;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $bank;
    /**
     * @var MoneyValidator
     */
    private $validator;

    public function testValidationShouldBeSuccessfulForNull()
    {
        $this->bank->expects($this->never())
            ->method('reduce');

        $this->context->expects($this->never())
            ->method('addViolation');

        $this->validator->validate(null, new MoneyConstraint(array('constraints' => array())));
    }

    /**
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     */
    public function testExceptionShouldBeRaisedForUnexpectedTypeOfValue()
    {
        $this->bank->expects($this->never())
            ->method('reduce');

        $this->context->expects($this->never())
            ->method('addViolation');

        $this->validator->validate(155, new MoneyConstraint(array('constraints' => array())));
    }

    public function testMoneyValidationWithoutCurrencyDefinitionInConstraint()
    {
        $money = $this->getMockMoney(32.45);

        $this->bank->expects($this->never())
            ->method('reduce');

        $maxConstraint = new Max(array('limit' => 33));

        $this->walker->expects($this->once())
            ->method('walkConstraint')
            ->with($maxConstraint, 32.45, 'MyGroup', 'foo.bar');

        $this->context->expects($this->never())
            ->method('addViolation');

        $moneyConstraint = new MoneyConstraint(array($maxConstraint));

        $this->validator->validate($money, $moneyConstraint);
    }

    public function testMoneyShouldBeReducedIfCurrencyDefinedInConstraint()
    {
        $moneyForValidation = $this->getMockMoney(300);
        $moneyReduced = $this->getMockMoney(10.25);

        $this->bank->expects($this->once())
            ->method('reduce')
            ->with($moneyForValidation, 'USD')
            ->will($this->returnValue($moneyReduced));

        $minConstraint = new Min(array('limit' => 15));

        $this->walker->expects($this->once())
            ->method('walkConstraint')
            ->with($minConstraint, 10.25, 'MyGroup', 'foo.bar');

        $this->context->expects($this->never())
            ->method('addViolation');

        $moneyConstraint = new MoneyConstraint(array('currency' => 'USD', 'constraints' => array($minConstraint)));

        $this->validator->validate($moneyForValidation, $moneyConstraint);
    }

    public function testMoneyValidationWithMultipleConstraints()
    {
        $moneyForValidation = $this->getMockMoney(15);
        $moneyReduced = $this->getMockMoney(463.72);

        $this->bank->expects($this->once())
            ->method('reduce')
            ->with($moneyForValidation, 'RUR')
            ->will($this->returnValue($moneyReduced));

        $minConstraint = new Min(600);
        $maxConstraint = new Max(850);

        $this->walker->expects($this->at(0))
            ->method('walkConstraint')
            ->with(
                $this->equalTo($minConstraint),
                $this->equalTo(463.72),
                $this->equalTo('MyGroup'),
                $this->equalTo('foo.bar')
            );
        $this->walker->expects($this->at(1))
            ->method('walkConstraint')
            ->with(
                $this->equalTo($maxConstraint),
                $this->equalTo(463.72),
                $this->equalTo('MyGroup'),
                $this->equalTo('foo.bar')
            );

        $this->context->expects($this->never())
            ->method('addViolation');

        $moneyConstraint = new MoneyConstraint(array('currency' => 'RUR', 'constraints' => array(
            $minConstraint,
            $maxConstraint
        )));

        $this->validator->validate($moneyForValidation, $moneyConstraint);
    }

    protected function setUp()
    {
        $this->walker = $this->getMock('Symfony\Component\Validator\GraphWalker', array(), array(), '', false);
        $this->context = $this->getMock('Symfony\Component\Validator\ExecutionContext', array(), array(), '', false);

        $this->context->expects($this->any())
            ->method('getGraphWalker')
            ->will($this->returnValue($this->walker));
        $this->context->expects($this->any())
            ->method('getGroup')
            ->will($this->returnValue('MyGroup'));
        $this->context->expects($this->any())
            ->method('getPropertyPath')
            ->will($this->returnValue('foo.bar'));

        $this->bank = $this->getMockBuilder('Dough\Bank\BankInterface')
            ->getMockForAbstractClass();

        $this->validator = new MoneyValidator($this->bank);
        $this->validator->initialize($this->context);
    }

    protected function tearDown()
    {
        unset($this->context, $this->bank, $this->validator);
    }

    /**
     * @param float $amount
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getMockMoney($amount)
    {
        $mock = $this->getMockBuilder('Dough\Money\Money')
            ->disableOriginalConstructor()
            ->getMock();

        $mock->expects($this->any())
            ->method('getAmount')
            ->will($this->returnValue($amount));

        return $mock;
    }
}