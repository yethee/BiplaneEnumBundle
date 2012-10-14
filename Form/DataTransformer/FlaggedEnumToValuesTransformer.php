<?php

namespace Biplane\EnumBundle\Form\DataTransformer;

use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Biplane\EnumBundle\Enumeration\FlaggedEnum;

/**
 * Transforms between a bit flags and the flagged enumeration instance.
 *
 * @author Denis Vasilev <yethee@biplane.ru>
 */
class FlaggedEnumToValuesTransformer extends BaseEnumTransformer
{
    /**
     * Transforms a FlaggedEnum objects to an array of bit flags.
     *
     * @param FlaggedEnum $value A FlaggedEnum instance
     *
     * @return array An array of bit flags
     *
     * @throws UnexpectedTypeException When $value is not the flagged enumeration
     */
    public function transform($value)
    {
        if ($value === null) {
            return null;
        }

        if (!$value instanceof $this->enumClass) {
            throw new UnexpectedTypeException($value, $this->enumClass);
        }

        return $value->getFlags();
    }

    /**
     * Transforms an array of raw values to the flagged enumeration object.
     *
     * @param array $values An array of raw values
     *
     * @return FlaggedEnum|null A FlaggedEnum instance or null
     *
     * @throws UnexpectedTypeException       When $values is not array
     * @throws TransformationFailedException When any value is not the integer type
     */
    public function reverseTransform($values)
    {
        if (!is_array($values)) {
            throw new UnexpectedTypeException($values, 'array');
        }

        if (count($values) == 0) {
            return null;
        }

        $rawValue = 0;

        foreach ($values as $value) {
            if (!is_integer($value)) {
                throw new TransformationFailedException(sprintf(
                    'The value "%s" (type of %s) must be the integer type.', $value, gettype($value)
                ));
            }

            $rawValue |= $value;
        }

        return $this->createEnum($rawValue);
    }
}