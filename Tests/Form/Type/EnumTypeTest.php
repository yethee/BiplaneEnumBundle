<?php

namespace Biplane\EnumBundle\Tests\Form\Type;

use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Tests\Extension\Core\Type\TypeTestCase;
use Biplane\EnumBundle\Form\Type\EnumType;
use Biplane\EnumBundle\Tests\Fixtures\SimpleEnum;
use Biplane\EnumBundle\Tests\Fixtures\FlagsEnum;

class EnumTypeTest extends TypeTestCase
{
    const SIMPLE_ENUM_CLASS = 'Biplane\\EnumBundle\\Tests\\Fixtures\\SimpleEnum';
    const FLAGS_ENUM_CLASS = 'Biplane\\EnumBundle\\Tests\\Fixtures\\FlagsEnum';

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testThrowExceptionWhenOptionEnumClassIsMissing()
    {
        $this->factory->create('biplane_enum');
    }

    /**
     * @expectedException \Symfony\Component\Form\Exception\FormException
     * @expectedExceptionMessage Enum class "Biplane\EnumBundle\Tests\Form\Type\EnumTypeTest" must be implements of Biplane\EnumBundle\Enumeration\EnumInterface.
     */
    public function testThrowExceptionWhenSpecifiedEnumClassNotImplementEnumInterface()
    {
        $this->factory->create('biplane_enum', null, array(
            'enum_class' => __CLASS__
        ));
    }

    /**
     * @expectedException \Symfony\Component\Form\Exception\FormException
     * @expectedExceptionMessage The "enum_class" (InvalidClass) does not exist.
     */
    public function testThrowExceptionWhenSpecifiedEnumClassDoesNotExists()
    {
        $this->factory->create('biplane_enum', null, array(
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
        $this->assertEquals(array(), $field->getNormData());
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

        $data = array(SimpleEnum::create(1));

        $this->assertTrue($field->isSynchronized());
        $this->assertEquals($data, $field->getData());
        $this->assertEquals(array(1), $field->getNormData());
        $this->assertTrue($field['1']->getData());
        $this->assertFalse($field['2']->getData());
        $this->assertSame('1', $field['1']->getClientData());
        $this->assertNull($field['2']->getClientData());
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
        $this->assertEquals(array(1, 2), $field->getNormData());
        $this->assertTrue($field['0']->getData());
        $this->assertTrue($field['1']->getData());
        $this->assertFalse($field['2']->getData());
        $this->assertFalse($field['3']->getData());
        $this->assertSame('1', $field['0']->getClientData());
        $this->assertSame('2', $field['1']->getClientData());
        $this->assertNull($field['2']->getClientData());
        $this->assertNull($field['3']->getClientData());
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
        $this->assertEquals(array(1, 4), $field->getNormData());
        $this->assertEquals(array('0' => true, '1' => false, '2' => true, '3' => false), $field->getClientData());
    }

    protected function setUp()
    {
        parent::setUp();

        $this->factory->addType(new EnumType());
    }
}