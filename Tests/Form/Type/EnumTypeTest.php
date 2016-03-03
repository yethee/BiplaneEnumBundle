<?php

namespace Biplane\EnumBundle\Tests\Form\Type;

use Biplane\EnumBundle\Enumeration\FlaggedEnum;
use Biplane\EnumBundle\Form\EnumExtension;
use Biplane\EnumBundle\Tests\Fixtures\FlagsEnum;
use Biplane\EnumBundle\Tests\Fixtures\SimpleEnum;
use Symfony\Component\Form\Test\FormIntegrationTestCase;

class EnumTypeTest extends FormIntegrationTestCase
{
    const SIMPLE_ENUM_CLASS = 'Biplane\\EnumBundle\\Tests\\Fixtures\\SimpleEnum';
    const FLAGS_ENUM_CLASS = 'Biplane\\EnumBundle\\Tests\\Fixtures\\FlagsEnum';

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testThrowExceptionWhenOptionEnumClassIsMissing()
    {
        $this->factory->create($this->getType());
    }

    /**
     * @expectedException \Symfony\Component\Form\Exception\InvalidConfigurationException
     * @expectedExceptionMessage Enum class "Biplane\EnumBundle\Tests\Form\Type\EnumTypeTest" must be implements of Biplane\EnumBundle\Enumeration\EnumInterface.
     */
    public function testThrowExceptionWhenSpecifiedEnumClassNotImplementEnumInterface()
    {
        $this->factory->create($this->getType(), null, array(
            'enum_class' => __CLASS__
        ));
    }

    /**
     * @expectedException \Symfony\Component\Form\Exception\InvalidConfigurationException
     * @expectedExceptionMessage The "enum_class" (InvalidClass) does not exist.
     */
    public function testThrowExceptionWhenSpecifiedEnumClassDoesNotExists()
    {
        $this->factory->create($this->getType(), null, array(
            'enum_class' => 'InvalidClass'
        ));
    }

    /**
     * @expectedException \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function testThrowExceptionWhenAppDataNotArrayForMultipleChoices()
    {
        $field = $this->factory->create($this->getType(), null, array(
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
        $field = $this->factory->create($this->getType(), null, array(
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
        $field = $this->factory->create($this->getType(), null, array(
            'enum_class' => self::SIMPLE_ENUM_CLASS
        ));

        $field->setData(1);
    }

    public function testBindSingleNull()
    {
        $field = $this->factory->create($this->getType(), null, array(
            'enum_class' => self::SIMPLE_ENUM_CLASS
        ));

        $field->submit(null);

        $this->assertTrue($field->isSynchronized());
        $this->assertNull($field->getData());
        $this->assertSame('', $field->getViewData());
    }

    public function testBindSingle()
    {
        $field = $this->factory->create($this->getType(), null, array(
            'enum_class' => self::SIMPLE_ENUM_CLASS
        ));

        $field->submit('1');

        $this->assertTrue($field->isSynchronized());
        $this->assertEquals(SimpleEnum::create(1), $field->getData());
        $this->assertSame('1', $field->getViewData());
    }

    public function testBindMultipleNull()
    {
        $field = $this->factory->create($this->getType(), null, array(
            'multiple' => true,
            'enum_class' => self::SIMPLE_ENUM_CLASS
        ));

        $field->submit(null);

        $this->assertEquals(array(), $field->getData());
        $this->assertEquals(array(), $field->getViewData());
    }

    public function testBindMultipleNull_FlagEnum()
    {
        $field = $this->factory->create($this->getType(), null, array(
            'multiple' => true,
            'enum_class' => self::FLAGS_ENUM_CLASS
        ));

        $field->submit(null);

        $this->assertInstanceOf(self::FLAGS_ENUM_CLASS, $field->getData());
        $this->assertEquals(FlaggedEnum::NONE, $field->getData()->getValue());
        $this->assertEquals(array(), $field->getNormData());
        $this->assertEquals(array(), $field->getViewData());
    }

    public function testBindMultipleExpanded()
    {
        $field = $this->factory->create($this->getType(), null, array(
            'multiple' => true,
            'expanded' => true,
            'enum_class' => self::SIMPLE_ENUM_CLASS
        ));

        $field->submit(array('1' => '1'));

        $data = array(SimpleEnum::create(1));

        $this->assertTrue($field->isSynchronized());
        $this->assertEquals($data, $field->getData());
        $this->assertEquals(array(1), $field->getNormData());
        $this->assertTrue($field['1']->getData());
        $this->assertFalse($field['2']->getData());
        $this->assertSame('1', $field['1']->getViewData());
        $this->assertNull($field['2']->getViewData());
    }

    public function testBindMultipleExpanded_FlagEnum()
    {
        $field = $this->factory->create($this->getType(), null, array(
            'multiple' => true,
            'expanded' => true,
            'enum_class' => self::FLAGS_ENUM_CLASS
        ));

        $field->submit(array('0' => '1', '1' => '2'));

        $this->assertTrue($field->isSynchronized());
        $this->assertEquals(FlagsEnum::create(1 | 2), $field->getData());
        $this->assertEquals(array(1, 2), $field->getNormData());
        $this->assertTrue($field['0']->getData());
        $this->assertTrue($field['1']->getData());
        $this->assertFalse($field['2']->getData());
        $this->assertFalse($field['3']->getData());
        $this->assertSame('1', $field['0']->getViewData());
        $this->assertSame('2', $field['1']->getViewData());
        $this->assertNull($field['2']->getViewData());
        $this->assertNull($field['3']->getViewData());
    }

    public function testSetDataSingleNull()
    {
        $field = $this->factory->create($this->getType(), null, array(
            'enum_class' => self::SIMPLE_ENUM_CLASS
        ));

        $field->setData(null);

        $this->assertNull($field->getData());
        $this->assertEquals('', $field->getViewData());
    }

    public function testSetDataMultipleExpandedNull()
    {
        $field = $this->factory->create($this->getType(), null, array(
            'multiple' => true,
            'expanded' => true,
            'enum_class' => self::SIMPLE_ENUM_CLASS
        ));

        $field->setData(null);

        $this->assertNull($field->getData());
        $this->assertEquals(array(), $field->getViewData());
    }

    public function testSetDataMultipleNonExpandedNull()
    {
        $field = $this->factory->create($this->getType(), null, array(
            'multiple' => true,
            'expanded' => false,
            'enum_class' => self::SIMPLE_ENUM_CLASS
        ));

        $field->setData(null);

        $this->assertNull($field->getData());
        $this->assertEquals(array(), $field->getViewData());
    }

    public function testSetDataSingle()
    {
        $data = SimpleEnum::create(1);
        $field = $this->factory->create($this->getType(), null, array(
            'enum_class' => self::SIMPLE_ENUM_CLASS
        ));

        $field->setData($data);

        $this->assertEquals($data, $field->getData());
        $this->assertSame('1', $field->getViewData());
    }

    public function testSetDataMultipleExpanded()
    {
        $data = array(SimpleEnum::create(1));
        $field = $this->factory->create($this->getType(), null, array(
            'multiple' => true,
            'expanded' => true,
            'enum_class' => self::SIMPLE_ENUM_CLASS
        ));

        $field->setData($data);

        $this->assertEquals($data, $field->getData());
        $this->assertSame(array(0 => '1'), $field->getViewData());
    }

    public function testSetDataExpanded()
    {
        $data = SimpleEnum::create(1);
        $field = $this->factory->create($this->getType(), null, array(
            'multiple' => false,
            'expanded' => true,
            'enum_class' => self::SIMPLE_ENUM_CLASS
        ));

        $field->setData($data);

        $this->assertEquals($data, $field->getData());
        $this->assertEquals(1, $field->getNormData());
        $this->assertSame('1', $field->getViewData());
    }

    public function testSetDataMultipleExpanded_FlagEnum()
    {
        $data = FlagsEnum::create(1 | 4);
        $field = $this->factory->create($this->getType(), null, array(
            'expanded' => true,
            'enum_class' => self::FLAGS_ENUM_CLASS
        ));

        $field->setData($data);

        $this->assertEquals($data, $field->getData());
        $this->assertEquals(array(1, 4), $field->getNormData());
        $this->assertEquals(array(0 => 1, 1 => 4), $field->getViewData());
    }

    protected function getExtensions()
    {
        return array_merge(parent::getExtensions(), array(
            new EnumExtension(),
        ));
    }

    private function getType()
    {
        if (method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix')) {
            return 'Biplane\EnumBundle\Form\Type\EnumType';
        }

        return 'biplane_enum';
    }
}
