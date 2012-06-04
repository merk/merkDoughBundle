<?php

/*
 * This file is part of the merkDoughBundle package.
 *
 * (c) Tim Nagel <tim@nagel.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace merk\DoughBundle\Tests\Form\Type;

use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Extension\Core\CoreExtension;
use Dough\Money\Money;
use merk\DoughBundle\Form\Type\MoneyType;

class MoneyTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FormFactory
     */
    private $factory;

    /**
     * @var FormBuilder
     */
    private $builder;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $dispatcher;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $bank;

    public function testBind()
    {
        $amount = 1.33;
        $money = new Money($amount);

        $this->bank->expects($this->once())
            ->method('createMoney')
            ->with($this->equalTo($amount), $this->equalTo('DEM'))
            ->will($this->returnValue($money));

        $form = $this->factory->create('merk_dough_money', null, array(
            'currency' => 'DEM'
        ));

        $form->bind('1.33');

        $this->assertTrue($form->isSynchronized());
        $this->assertSame($money, $form->getData());
        $this->assertEquals($amount, $form->getNormData());
        $this->assertEquals('1.33', $form->getViewData());
    }

    public function testBindNull()
    {
        $this->bank->expects($this->never())->method('createMoney');

        $form = $this->factory->create('merk_dough_money', null);

        $form->bind(null);

        $this->assertTrue($form->isSynchronized());
        $this->assertNull($form->getData());
        $this->assertNull($form->getNormData());
        $this->assertEmpty($form->getViewData());
    }

    public function testSetData()
    {
        $money = new Money(7.99);
        $currency = 'DEM';

        $this->bank->expects($this->once())
            ->method('reduce')
            ->with($this->equalTo($money), $this->equalTo($currency))
            ->will($this->returnArgument(0));

        $form = $this->factory->create('merk_dough_money', null, array(
            'currency' => $currency
        ));

        $form->setData($money);

        $this->assertSame($money, $form->getData());
        $this->assertEquals(7.99, $form->getNormData());
        $this->assertEquals('7.99', $form->getViewData());
    }

    public function testSetDataNull()
    {
        $this->bank->expects($this->never())->method('reduce');

        $form = $this->factory->create('merk_dough_money');

        $this->assertNull($form->getData());
        $this->assertNull($form->getNormData());
        $this->assertEmpty($form->getViewData());
    }

    protected function setUp()
    {
        if (!extension_loaded('intl')) {
            $this->markTestSkipped('The "intl" extension is not available');
        }

        \Locale::setDefault('en_US');

        $this->dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $this->bank = $this->getMock('Dough\Bank\BankInterface');

        $this->factory = new FormFactory(array(new CoreExtension()));
        $this->builder = new FormBuilder(null, null, $this->dispatcher, $this->factory);

        $this->factory->addType(new MoneyType($this->bank));
    }

    protected function tearDown()
    {
        unset($this->factory, $this->builder, $this->dispatcher, $this->bank);
    }
}