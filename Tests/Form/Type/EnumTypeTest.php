<?php

namespace Biplane\EnumBundle\Tests\Form\Type;

use Biplane\EnumBundle\Enumeration\FlaggedEnum;
use Biplane\EnumBundle\Form\EnumExtension;
use Biplane\EnumBundle\Form\Type\EnumType;
use Biplane\EnumBundle\Tests\Fixtures\FlagsEnum;
use Biplane\EnumBundle\Tests\Fixtures\SimpleEnum;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Test\FormIntegrationTestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

class EnumTypeTest extends FormIntegrationTestCase
{
    public function testThrowExceptionWhenOptionEnumClassIsMissing(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $this->factory->create($this->getType());
    }

    public function testThrowExceptionWhenSpecifiedEnumClassNotImplementEnumInterface(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Enum class "Biplane\EnumBundle\Tests\Form\Type\EnumTypeTest" must be implements of Biplane\EnumBundle\Enumeration\EnumInterface');

        $this->factory->create($this->getType(), null, array(
            'enum_class' => __CLASS__
        ));
    }

    public function testThrowExceptionWhenSpecifiedEnumClassDoesNotExists(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('The "enum_class" (InvalidClass) does not exist.');

        $this->factory->create($this->getType(), null, array(
            'enum_class' => 'InvalidClass'
        ));
    }

    public function testThrowExceptionWhenAppDataNotArrayForMultipleChoices(): void
    {
        $field = $this->factory->create($this->getType(), null, array(
            'multiple' => true,
            'enum_class' => SimpleEnum::class,
        ));

        $this->expectException(UnexpectedTypeException::class);

        $field->setData('1');
    }

    public function testThrowExcetionWhenAppDataIsInvalidForMultipleChoices(): void
    {
        $field = $this->factory->create($this->getType(), null, array(
            'multiple' => true,
            'enum_class' => SimpleEnum::class,
        ));

        $this->expectException(TransformationFailedException::class);

        $field->setData(array(
            SimpleEnum::create(1),
            2
        ));
    }

    public function testThrowExcetionWhenAppDataIsInvalidForSingleChoice(): void
    {
        $field = $this->factory->create($this->getType(), null, array(
            'enum_class' => SimpleEnum::class,
        ));

        $this->expectException(UnexpectedTypeException::class);

        $field->setData(1);
    }

    public function testBindSingleNull(): void
    {
        $field = $this->factory->create($this->getType(), null, array(
            'enum_class' => SimpleEnum::class,
        ));

        $field->submit(null);

        self::assertTrue($field->isSynchronized());
        self::assertNull($field->getData());
        self::assertSame('', $field->getViewData());
    }

    public function testBindSingle(): void
    {
        $field = $this->factory->create($this->getType(), null, array(
            'enum_class' => SimpleEnum::class,
        ));

        $field->submit('1');

        self::assertTrue($field->isSynchronized());
        self::assertEquals(SimpleEnum::create(1), $field->getData());
        self::assertSame('1', $field->getViewData());
    }

    public function testBindMultipleNull(): void
    {
        $field = $this->factory->create($this->getType(), null, array(
            'multiple' => true,
            'enum_class' => SimpleEnum::class,
        ));

        $field->submit(null);

        self::assertEquals(array(), $field->getData());
        self::assertEquals(array(), $field->getViewData());
    }

    public function testBindMultipleNull_FlagEnum(): void
    {
        $field = $this->factory->create($this->getType(), null, array(
            'multiple' => true,
            'enum_class' => FlagsEnum::class,
        ));

        $field->submit(null);

        self::assertInstanceOf(FlagsEnum::class, $field->getData());
        self::assertEquals(FlaggedEnum::NONE, $field->getData()->getValue());
        self::assertEquals(array(), $field->getNormData());
        self::assertEquals(array(), $field->getViewData());
    }

    public function testBindMultipleExpanded(): void
    {
        $field = $this->factory->create($this->getType(), null, array(
            'multiple' => true,
            'expanded' => true,
            'enum_class' => SimpleEnum::class,
        ));

        $field->submit(array('1' => '1'));

        $data = array(SimpleEnum::create(1));

        self::assertTrue($field->isSynchronized());
        self::assertEquals($data, $field->getData());
        self::assertEquals(array(1), $field->getNormData());
        self::assertTrue($field['1']->getData());
        self::assertFalse($field['2']->getData());
        self::assertSame('1', $field['1']->getViewData());
        self::assertNull($field['2']->getViewData());
    }

    public function testBindMultipleExpanded_FlagEnum(): void
    {
        $field = $this->factory->create($this->getType(), null, array(
            'multiple' => true,
            'expanded' => true,
            'enum_class' => FlagsEnum::class,
        ));

        $field->submit(array('0' => '1', '1' => '2'));

        self::assertTrue($field->isSynchronized());
        self::assertEquals(FlagsEnum::create(1 | 2), $field->getData());
        self::assertEquals(array(1, 2), $field->getNormData());
        self::assertTrue($field['0']->getData());
        self::assertTrue($field['1']->getData());
        self::assertFalse($field['2']->getData());
        self::assertFalse($field['3']->getData());
        self::assertSame('1', $field['0']->getViewData());
        self::assertSame('2', $field['1']->getViewData());
        self::assertNull($field['2']->getViewData());
        self::assertNull($field['3']->getViewData());
    }

    public function testSetDataSingleNull(): void
    {
        $field = $this->factory->create($this->getType(), null, array(
            'enum_class' => SimpleEnum::class,
        ));

        $field->setData(null);

        self::assertNull($field->getData());
        self::assertEquals('', $field->getViewData());
    }

    public function testSetDataMultipleExpandedNull(): void
    {
        $field = $this->factory->create($this->getType(), null, array(
            'multiple' => true,
            'expanded' => true,
            'enum_class' => SimpleEnum::class,
        ));

        $field->setData(null);

        self::assertNull($field->getData());
        self::assertEquals(array(), $field->getViewData());

        foreach ($field->all() as $child) {
            self::assertSubForm($child, false, null);
        }
    }

    public function testSetDataMultipleNonExpandedNull(): void
    {
        $field = $this->factory->create($this->getType(), null, array(
            'multiple' => true,
            'expanded' => false,
            'enum_class' => SimpleEnum::class,
        ));

        $field->setData(null);

        self::assertNull($field->getData());
        self::assertEquals(array(), $field->getViewData());
    }

    public function testSetDataSingle(): void
    {
        $data = SimpleEnum::create(1);
        $field = $this->factory->create($this->getType(), null, array(
            'enum_class' => SimpleEnum::class,
        ));

        $field->setData($data);

        self::assertEquals($data, $field->getData());
        self::assertEquals('1', $field->getViewData());
    }

    public function testSetDataMultipleExpanded(): void
    {
        $data = array(
            SimpleEnum::create(SimpleEnum::FIRST),
            SimpleEnum::create(SimpleEnum::ZERO),
        );
        $field = $this->factory->create($this->getType(), null, array(
            'multiple' => true,
            'expanded' => true,
            'enum_class' => SimpleEnum::class,
        ));

        $field->setData($data);

        self::assertEquals($data, $field->getData());
        self::assertSame(array(
            0 => '1',
            1 => '0'
        ), $field->getViewData());

        self::assertSubForm($field->get('0'), true, '0');
        self::assertSubForm($field->get('1'), true, '1');
        self::assertSubForm($field->get('2'), false, null);
    }

    public function testSetDataExpanded(): void
    {
        $data = SimpleEnum::create(1);
        $field = $this->factory->create($this->getType(), null, array(
            'multiple' => false,
            'expanded' => true,
            'enum_class' => SimpleEnum::class,
        ));

        $field->setData($data);

        self::assertEquals($data, $field->getData());
        self::assertSame('1', $field->getNormData());
        self::assertSame('1', $field->getViewData());

        self::assertSubForm($field->get('0'), false, null);
        self::assertSubForm($field->get('1'), true, '1');
        self::assertSubForm($field->get('2'), false, null);
    }

    public function testSetDataMultipleExpanded_FlagEnum(): void
    {
        $data = FlagsEnum::create(1 | 4);
        $field = $this->factory->create($this->getType(), null, array(
            'expanded' => true,
            'enum_class' => FlagsEnum::class,
        ));

        $field->setData($data);

        self::assertEquals($data, $field->getData());
        self::assertEquals(array(1, 4), $field->getNormData());
        self::assertEquals(array(0 => 1, 1 => 4), $field->getViewData());

        self::assertSubForm($field->get('0'), true, '1');
        self::assertSubForm($field->get('1'), false, null);
        self::assertSubForm($field->get('2'), true, '4');
        self::assertSubForm($field->get('3'), false, null);
    }

    protected function getExtensions()
    {
        return array_merge(parent::getExtensions(), array(
            new EnumExtension(),
        ));
    }

    private function getType(): string
    {
        return EnumType::class;
    }

    private static function assertSubForm(FormInterface $form, $data, $viewData)
    {
        self::assertSame($data, $form->getData(), '->getData() of sub form #' . $form->getName());
        self::assertSame($viewData, $form->getViewData(), '->getViewData() of sub form #' . $form->getName());
    }
}
