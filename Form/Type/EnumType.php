<?php

namespace Biplane\EnumBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Options;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Exception\FormException;
use Biplane\EnumBundle\Form\DataTransformer\ValueToEnumTransformer;
use Biplane\EnumBundle\Form\DataTransformer\ValuesToEnumsTransformer;
use Biplane\EnumBundle\Form\DataTransformer\ValuesToFlaggedEnumTransformer;

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
     * @see FormTypeExtensionInterface::buildForm()
     *
     * @param FormBuilder $builder The form builder
     * @param array       $options The options
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        if (!$options['enum_class']) {
            throw new FormException('The option "enum_class" is required.');
        }

        try {
            if ($options['multiple']) {
                if (is_subclass_of($options['enum_class'], 'Biplane\EnumBundle\Enumeration\FlaggedEnum')) {
                    $builder->appendNormTransformer(new ValuesToFlaggedEnumTransformer($options['enum_class']));
                } else {
                    $builder->prependClientTransformer(new ValuesToEnumsTransformer($options['enum_class']));
                }
            } else {
                $builder->prependClientTransformer(new ValueToEnumTransformer($options['enum_class']));
            }
        } catch (\InvalidArgumentException $ex) {
            throw new FormException($ex->getMessage());
        } catch (\ReflectionException $ex) {
            throw new FormException(sprintf('The "enum_class" (%s) does not exist.', $options['enum_class']));
        }
    }

    /**
     * Returns the default options for this type.
     *
     * @param array $options
     *
     * @return array The default options
     */
    public function getDefaultOptions()
    {
        return array(
            'enum_class' => null,
            'choices'    => function (Options $options) {
                if (!empty($options['enum_class']) && method_exists($options['enum_class'], 'getReadables')) {
                    return $options['enum_class']::getReadables();
                }
                return array();
            },
        );
    }

    /**
     * Returns the name of the parent type.
     *
     * @param array $options
     *
     * @return string|null The name of the parent type if any otherwise null
     */
    public function getParent(array $options)
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
