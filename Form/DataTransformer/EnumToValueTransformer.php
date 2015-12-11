<?php

namespace Biplane\EnumBundle\Form\DataTransformer;

use Biplane\EnumBundle\Enumeration\EnumInterface;
use Biplane\EnumBundle\Exception\InvalidEnumArgumentException;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

/**
 * Transforms between an enumeration instance and a raw value.
 *
 * @author Denis Vasilev <yethee@biplane.ru>
 */
class EnumToValueTransformer extends BaseEnumTransformer
{
    /**
     * Transforms a raw value to enumeration object.
     *
     * @param mixed $value A raw value of enumeration
     *
     * @return EnumInterface An EnumInterface instance or null
     *
     * @throws UnexpectedTypeException       When $value is not valid type
     * @throws TransformationFailedException When $value is not acceptable for enumeration
     */
    public function reverseTransform($value)
    {
        if ($value === null) {
            return null;
        }

        if (null !== $value && !is_scalar($value)) {
            throw new UnexpectedTypeException($value, 'scalar');
        }

        try {
            return $this->createEnum($value);
        } catch (InvalidEnumArgumentException $ex) {
            throw new TransformationFailedException(sprintf(
                'The value "%s" is not acceptable for enumeration of %s type.',
                $value,
                $this->enumClass
            ));
        }
    }

    /**
     * Transforms enumeration object to the raw value.
     *
     * @param EnumInterface|null $value An EnumInterface instance
     *
     * @return mixed A scalar value
     *
     * @throws UnexpectedTypeException When $value is not valid type
     */
    public function transform($value)
    {
        if ($value === null) {
            return null;
        }

        if (!$value instanceof $this->enumClass) {
            throw new UnexpectedTypeException($value, $this->enumClass);
        }

        return $value->getValue();
    }
}
