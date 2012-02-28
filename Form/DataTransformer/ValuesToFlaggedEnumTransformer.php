<?php

namespace Biplane\EnumBundle\Form\DataTransformer;

use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Biplane\EnumBundle\Enumeration\FlaggedEnum;

/**
 * Transforms between a raw values and the flagged enumeration instance.
 *
 * @author Denis Vasilev <yethee@biplane.ru>
 */
class ValuesToFlaggedEnumTransformer extends BaseEnumTransformer
{
    /**
     * Transforms an array of enumeration objects to a raw values.
     *
     * @param FlaggedEnum $value A FlaggedEnum instance
     *
     * @return array An array of raw values
     *
     * @throws UnexpectedTypeException When $value is the flagged enumeration
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
     * @return FlaggedEnum|null A FlaggedEnum instance of null
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