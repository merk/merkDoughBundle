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
        $money = new Money(1.33);

        $this->bank->expects($this->once())
            ->method('createMoney')
            ->with($this->equalTo(1.33), $this->equalTo('DEM'))
            ->will($this->returnValue($money));
        $this->bank->expects($this->once())
            ->method('reduce')
            ->with($this->equalTo($money), $this->equalTo('DEM'))
            ->will($this->returnArgument(0));

        $form = $this->factory->create('merk_dough_money', null, array(
            'currency' => 'DEM'
        ));

        $form->bind('1.33');

        $this->assertTrue($form->isSynchronized());
        $this->assertSame($money, $form->getData());
        $this->assertEquals('1.33', $form->getClientData());
    }

    public function testBindNull()
    {
        $this->bank->expects($this->never())->method('createMoney');
        $this->bank->expects($this->never())->method('reduce');

        $form = $this->factory->create('merk_dough_money', null);

        $form->bind(null);

        $this->assertTrue($form->isSynchronized());
        $this->assertNull($form->getData());
        $this->assertEmpty($form->getClientData());
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
        $this->builder = new FormBuilder(null, $this->factory, $this->dispatcher);

        $this->factory->addType(new MoneyType($this->bank));
    }

    protected function tearDown()
    {
        unset($this->factory, $this->builder, $this->dispatcher, $this->bank);
    }
}