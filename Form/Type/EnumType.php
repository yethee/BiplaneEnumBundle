<?php

namespace Biplane\EnumBundle\Form\Type;

use Biplane\EnumBundle\Form\DataTransformer\EnumsToValuesTransformer;
use Biplane\EnumBundle\Form\DataTransformer\EnumToValueTransformer;
use Biplane\EnumBundle\Form\DataTransformer\FlaggedEnumToValuesTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * EnumType
 *
 * @author Denis Vasilev <yethee@biplane.ru>
 */
class EnumType extends AbstractType
{
    /**
     * Builds the form.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     *
     * @throws InvalidConfigurationException
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        try {
            $builder->resetModelTransformers();
            if ($options['multiple']) {
                if (is_subclass_of($options['enum_class'], 'Biplane\EnumBundle\Enumeration\FlaggedEnum')) {
                    $builder->addModelTransformer(new FlaggedEnumToValuesTransformer($options['enum_class']));
                } else {
                    $builder->addModelTransformer(new EnumsToValuesTransformer($options['enum_class']));
                }
            } else {
                $builder->addModelTransformer(new EnumToValueTransformer($options['enum_class']));
            }
        } catch (\InvalidArgumentException $ex) {
            throw new InvalidConfigurationException($ex->getMessage());
        } catch (\ReflectionException $ex) {
            throw new InvalidConfigurationException(
                sprintf(
                    'The "enum_class" (%s) does not exist.',
                    $options['enum_class']
                )
            );
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $choices = function (Options $options) {
            if ($options['enum_class'] !== null && method_exists($options['enum_class'], 'getReadables')) {
                return array_flip($options['enum_class']::getReadables());
            }

            return array();
        };

        $enumClass = function (Options $options) {
            if ($options->offsetExists('data') && is_object($enum = $options->offsetGet('data'))) {
                return get_class($enum);
            }

            return null;
        };

        $resolver
            ->setDefined(array('enum_class', 'choices'))
            ->setDefaults(
                array(
                    'enum_class' => $enumClass,
                    'choices' => $choices,
                    'choices_as_values' => true,
                )
            )
            ->setAllowedTypes('enum_class', array('string'))
            ->setAllowedTypes('choices', array('array'));
    }


    /**
     * Returns the name of the parent type.
     *
     * @return string|null The name of the parent type if any otherwise null
     */
    public function getParent()
    {
        return ChoiceType::class;
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'biplane_enum';
    }
}
