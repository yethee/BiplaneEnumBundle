<?php

namespace Biplane\EnumBundle\Tests\Form\Type;

use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\Extension\Core\CoreExtension;
use Biplane\EnumBundle\Form\Type\EnumType;
use Biplane\EnumBundle\Tests\Fixtures\SimpleEnum;
use Biplane\EnumBundle\Tests\Fixtures\FlagEnum;

/**
 * @author Denis Vasilev <yethee@biplane.ru>
 */
class EnumTypeTest extends \PHPUnit_Framework_TestCase
{
    const SIMPLE_ENUM_CLASS = 'Biplane\\EnumBundle\\Tests\\Fixtures\\SimpleEnum';
    const FLAG_ENUM_CLASS = 'Biplane\\EnumBundle\\Tests\\Fixtures\\FlagEnum';

    /**
     * @var \Symfony\Component\Form\FormFactory
     */
    protected $factory;
    /**
     * @var \Symfony\Component\Form\FormBuilder
     */
    protected $builder;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $dispatcher;

    public function testExceptionIsRaisedWhenOptionEnumClassIsMissing()
    {
        $this->setExpectedException(
            'Symfony\Component\Form\Exception\FormException',
            'The option "enum_class" is required.'
        );

        $this->factory->create('biplane_enum', null, array(
            'choices' => array('1' => '1')
        ));
    }

    public function testExceptionIsRaisedWhenEnumClassIsInvalid()
    {
        $this->setExpectedException(
            'Symfony\Component\Form\Exception\FormException',
            sprintf('Enum class "%s" must be implements of Biplane\EnumBundle\Enumeration\EnumInterface.', __CLASS__)
        );

        $this->factory->create('biplane_enum', null, array(
            'choices' => array('1' => '1'),
            'enum_class' => __CLASS__
        ));
    }

    public function testExceptionIsRaisedWhenEnumClassDoesNotExists()
    {
        $this->setExpectedException(
            'Symfony\Component\Form\Exception\FormException',
            'The "enum_class" (InvalidClass) does not exist.'
        );

        $this->factory->create('biplane_enum', null, array(
            'choices' => array('1' => '1'),
            'enum_class' => 'InvalidClass'
        ));
    }

    /**
     * @expectedException \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function testExceptionIsRaisedWhenSetDataExpectsArray()
    {
        $field = $this->factory->create('biplane_enum', null, array(
            'multiple' => true,
            'enum_class' => self::SIMPLE_ENUM_CLASS
        ));

        $field->setData('1');
    }

    /**
     * @expectedException \Symfony\Component\Form\Exception\TransformationFailedException
     */
    public function testExcetionIsRaisedWhenInvalidSetData()
    {
        $field = $this->factory->create('biplane_enum', null, array(
            'multiple' => true,
            'enum_class' => self::SIMPLE_ENUM_CLASS
        ));

        $field->setData(array(
            SimpleEnum::create(1),
            2
        ));
    }

    public function testBindSingleNull()
    {
        $field = $this->factory->create('biplane_enum', null, array(
            'multiple' => false,
            'enum_class' => self::SIMPLE_ENUM_CLASS
        ));

        $field->bind(null);

        $this->assertTrue($field->isSynchronized());
        $this->assertNull($field->getData());
        $this->assertSame('', $field->getClientData());
    }

    public function testBindSingle()
    {
        $selectedEnum = SimpleEnum::create(1);

        $field = $this->factory->create('biplane_enum', null, array(
            'multiple' => false,
            'enum_class' => self::SIMPLE_ENUM_CLASS
        ));

        $field->bind('1');

        $this->assertTrue($field->isSynchronized());
        $this->assertEquals($selectedEnum, $field->getData());
        $this->assertSame(1, $field->getClientData());
    }

    public function testBindMultipleNull()
    {
        $field = $this->factory->create('biplane_enum', null, array(
            'multiple' => true,
            'enum_class' => self::SIMPLE_ENUM_CLASS
        ));

        $field->bind(null);

        $this->assertEquals(array(), $field->getData());
        $this->assertEquals(array(), $field->getClientData());
    }

    public function testBindMultipleNull_FlagEnum()
    {
        $field = $this->factory->create('biplane_enum', null, array(
            'multiple' => true,
            'enum_class' => self::FLAG_ENUM_CLASS
        ));

        $field->bind(null);

        $this->assertNull($field->getData());
        $this->assertEquals(array(), $field->getClientData());
    }

    public function testBindMultipleExpanded()
    {
        $field = $this->factory->create('biplane_enum', null, array(
            'multiple' => true,
            'expanded' => true,
            'enum_class' => self::SIMPLE_ENUM_CLASS
        ));

        $field->bind(array('1' => '1'));

        $this->assertTrue($field->isSynchronized());
        $this->assertEquals(array(SimpleEnum::create(1)), $field->getData());
        $this->assertSame(true, $field['1']->getData());
        $this->assertSame(false, $field['2']->getData());
        $this->assertSame('1', $field['1']->getClientData());
        $this->assertSame('', $field['2']->getClientData());
    }

    public function testBindMultipleExpanded_FlagEnum()
    {
        $field = $this->factory->create('biplane_enum', null, array(
            'multiple' => true,
            'expanded' => true,
            'enum_class' => self::FLAG_ENUM_CLASS
        ));

        $field->bind(array('1' => '1', '2' => '2'));

        $this->assertTrue($field->isSynchronized());
        $this->assertEquals(FlagEnum::create(1 | 2), $field->getData());
        $this->assertSame(true, $field['1']->getData());
        $this->assertSame(true, $field['2']->getData());
        $this->assertSame(false, $field['4']->getData());
        $this->assertSame(false, $field['16']->getData());
        $this->assertSame('1', $field['1']->getClientData());
        $this->assertSame('1', $field['2']->getClientData());
        $this->assertSame('', $field['4']->getClientData());
        $this->assertSame('', $field['16']->getClientData());
    }

    public function testSetDataSingleNull()
    {
        $field = $this->factory->create('biplane_enum', null, array(
            'multiple' => false,
            'enum_class' => self::SIMPLE_ENUM_CLASS
        ));

        $field->setData(null);

        $this->assertNull($field->getData());
        $this->assertEquals('', $field->getClientData());
    }

    public function testSetDataMultipleExpandedNull()
    {
        $field = $this->factory->create('biplane_enum', null, array(
            'multiple' => true,
            'expanded' => true,
            'enum_class' => self::SIMPLE_ENUM_CLASS
        ));

        $field->setData(null);

        $this->assertNull($field->getData());
        $this->assertEquals(array('1' => false, '2' => false), $field->getClientData());
    }

    public function testSetDataMultipleNonExpandedNull()
    {
        $field = $this->factory->create('biplane_enum', null, array(
            'multiple' => true,
            'expanded' => false,
            'enum_class' => self::SIMPLE_ENUM_CLASS
        ));

        $field->setData(null);

        $this->assertNull($field->getData());
        $this->assertEquals(array(), $field->getClientData());
    }

    public function testSetDataSingle()
    {
        $data = SimpleEnum::create(1);
        $field = $this->factory->create('biplane_enum', null, array(
            'multiple' => false,
            'enum_class' => self::SIMPLE_ENUM_CLASS
        ));

        $field->setData($data);

        $this->assertEquals($data, $field->getData());
        $this->assertEquals('1', $field->getClientData());
    }

    public function testSetDataMultipleExpanded()
    {
        $data = array(SimpleEnum::create(1));
        $field = $this->factory->create('biplane_enum', null, array(
            'multiple' => true,
            'expanded' => true,
            'enum_class' => self::SIMPLE_ENUM_CLASS
        ));

        $field->setData($data);

        $this->assertEquals($data, $field->getData());
        $this->assertEquals(array('1' => true, '2' => false), $field->getClientData());
    }

    public function testSetDataMultipleExpanded_FlagEnum()
    {
        $data = FlagEnum::create(1 | 4);
        $field = $this->factory->create('biplane_enum', null, array(
            'multiple' => true,
            'expanded' => true,
            'enum_class' => self::FLAG_ENUM_CLASS
        ));

        $field->setData($data);

        $this->assertEquals($data, $field->getData());
        $this->assertEquals(array('1' => true, '2' => false, '4' => true, '16' => false), $field->getClientData());
    }


    protected function setUp()
    {
        $this->dispatcher = $this->getMock('Symfony\\Component\\EventDispatcher\\EventDispatcherInterface');
        $this->factory = new FormFactory(array(new CoreExtension()));
        $this->builder = new FormBuilder(null, $this->factory, $this->dispatcher);

        $this->factory->addType(new EnumType());
    }

    protected function tearDown()
    {
        $this->builder = null;
        $this->dispatcher = null;
        $this->factory = null;
    }
}