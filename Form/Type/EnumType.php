<?php

namespace Biplane\EnumBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Exception\FormException;
use Biplane\EnumBundle\Form\DataTransformer\ChoiceToEnumTransformer;

/**
 * EnumType
 *
 * @author Denis Vasilev <yethee@biplane.ru>
 */
class EnumType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        if (!$options['enum_class']) {
            throw new FormException('The option "enum_class" is required.');
        }

        try {
            $builder->appendNormTransformer(new ChoiceToEnumTransformer($options['enum_class'], $options['multiple']));
        }
        catch (\InvalidArgumentException $ex) {
            throw new FormException($ex->getMessage());
        }
        catch (\ReflectionException $ex) {
            throw new FormException(sprintf('The "enum_class" (%s) does not exist.', $options['enum_class']));
        }
    }

    public function getDefaultOptions(array $options)
    {
        $defaultOptions = array(
            'enum_class' => isset($options['enum_class']) ? (string)$options['enum_class'] : null
        );

        if (!empty($defaultOptions['enum_class']) && method_exists($defaultOptions['enum_class'], 'getReadables')) {
            $defaultOptions['choices'] = $defaultOptions['enum_class']::getReadables();
        }

        return $defaultOptions;
    }

    public function getParent(array $options)
    {
        return 'choice';
    }

    public function getName()
    {
        return 'biplane_enum';
    }
}