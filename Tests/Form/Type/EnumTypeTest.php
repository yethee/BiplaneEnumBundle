<?php

namespace Biplane\EnumBundle\Tests\Form\Type;

use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\Extension\Core\CoreExtension;
use Biplane\EnumBundle\Form\Type\EnumType;
use Biplane\EnumBundle\Tests\Fixtures\SimpleEnum;
use Biplane\EnumBundle\Tests\Fixtures\FlagsEnum;

/**
 * @author Denis Vasilev <yethee@biplane.ru>
 */
class EnumTypeTest extends \PHPUnit_Framework_TestCase
{
    const SIMPLE_ENUM_CLASS = 'Biplane\\EnumBundle\\Tests\\Fixtures\\SimpleEnum';
    const FLAGS_ENUM_CLASS = 'Biplane\\EnumBundle\\Tests\\Fixtures\\FlagsEnum';

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

    /**
     * @expectedException Symfony\Component\Form\Exception\FormException
     * @expectedExceptionMessage The option "enum_class" is required.
     */
    public function testThrowExceptionWhenOptionEnumClassIsMissing()
    {
        $this->factory->create('biplane_enum', null, array(
            'choices' => array('1' => '1')
        ));
    }

    /**
     * @expectedException Symfony\Component\Form\Exception\FormException
     * @expectedExceptionMessage Enum class "Biplane\EnumBundle\Tests\Form\Type\EnumTypeTest" must be implements of Biplane\EnumBundle\Enumeration\EnumInterface.
     */
    public function testThrowExceptionWhenSpecifiedEnumClassNotImplementEnumInterface()
    {
        $this->factory->create('biplane_enum', null, array(
            'choices' => array('1' => '1'),
            'enum_class' => __CLASS__
        ));
    }

    /**
     * @expectedException Symfony\Component\Form\Exception\FormException
     * @expectedExceptionMessage The "enum_class" (InvalidClass) does not exist.
     */
    public function testThrowExceptionWhenSpecifiedEnumClassDoesNotExists()
    {
        $this->factory->create('biplane_enum', null, array(
            'choices' => array('1' => '1'),
            'enum_class' => 'InvalidClass'
        ));
    }

    /**
     * @expectedException \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function testThrowExceptionWhenAppDataNotArrayForMultipleChoices()
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
    public function testThrowExcetionWhenAppDataIsInvalidForMultipleChoices()
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

    /**
     * @expectedException \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function testThrowExcetionWhenAppDataIsInvalidForSingleChoice()
    {
        $field = $this->factory->create('biplane_enum', null, array(
            'enum_class' => self::SIMPLE_ENUM_CLASS
        ));

        $field->setData(1);
    }

    public function testBindSingleNull()
    {
        $field = $this->factory->create('biplane_enum', null, array(
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
            'enum_class' => self::SIMPLE_ENUM_CLASS
        ));

        $field->bind('1');

        $this->assertTrue($field->isSynchronized());
        $this->assertEquals($selectedEnum, $field->getData());
        $this->assertSame('1', $field->getClientData());
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
            'enum_class' => self::FLAGS_ENUM_CLASS
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
            'enum_class' => self::FLAGS_ENUM_CLASS
        ));

        $field->bind(array('0' => '1', '1' => '2'));

        $this->assertTrue($field->isSynchronized());
        $this->assertEquals(FlagsEnum::create(1 | 2), $field->getData());
        $this->assertSame(true, $field['0']->getData());
        $this->assertSame(true, $field['1']->getData());
        $this->assertSame(false, $field['2']->getData());
        $this->assertSame(false, $field['3']->getData());
        $this->assertSame('1', $field['0']->getClientData());
        $this->assertSame('1', $field['1']->getClientData());
        $this->assertSame('', $field['2']->getClientData());
        $this->assertSame('', $field['3']->getClientData());
    }

    public function testSetDataSingleNull()
    {
        $field = $this->factory->create('biplane_enum', null, array(
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
        $this->assertEquals(array('0' => false, '1' => false, '2' => false), $field->getClientData());
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
        $this->assertEquals(array('0' => false, '1' => true, '2' => false), $field->getClientData());
    }

    public function testSetDataMultipleExpanded_FlagEnum()
    {
        $data = FlagsEnum::create(1 | 4);
        $field = $this->factory->create('biplane_enum', null, array(
            'multiple' => true,
            'expanded' => true,
            'enum_class' => self::FLAGS_ENUM_CLASS
        ));

        $field->setData($data);

        $this->assertEquals($data, $field->getData());
        $this->assertEquals(array('0' => true, '1' => false, '2' => true, '3' => false), $field->getClientData());
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