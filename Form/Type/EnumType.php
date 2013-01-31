<?php

namespace Biplane\EnumBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Biplane\EnumBundle\Form\DataTransformer\EnumToValueTransformer;
use Biplane\EnumBundle\Form\DataTransformer\EnumsToValuesTransformer;
use Biplane\EnumBundle\Form\DataTransformer\FlaggedEnumToValuesTransformer;

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
            throw new InvalidConfigurationException(sprintf(
                'The "enum_class" (%s) does not exist.', $options['enum_class']
            ));
        }
    }

    /**
     * Sets the default options for this type.
     *
     * @param OptionsResolverInterface $resolver The resolver for the options.
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $choices = function(Options $options) {
            if ($options['enum_class'] !== null && method_exists($options['enum_class'], 'getReadables')) {
                return $options['enum_class']::getReadables();
            }

            return array();
        };

        $enumClass = function(Options $options) {
            if ($options->has('data') && is_object($enum = $options->get('data'))) {
                return get_class($enum);
            }

            return null;
        };

        $resolver->setDefaults(array(
            'enum_class' => $enumClass,
            'choices'    => $choices,
        ));

        $resolver->setAllowedTypes(array(
            'enum_class' => 'string'
        ));
    }

    /**
     * Returns the name of the parent type.
     *
     * @return string|null The name of the parent type if any otherwise null
     */
    public function getParent()
    {
        return 'choice';
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
