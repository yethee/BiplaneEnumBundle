<?php

namespace Biplane\EnumBundle\Form\DataTransformer;

use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Biplane\EnumBundle\Enumeration\EnumInterface;
use Biplane\EnumBundle\Exception\InvalidEnumArgumentException;

/**
 * Transforms between raw values and enumeration instances.
 *
 * @author Denis Vasilev <yethee@biplane.ru>
 */
class EnumsToValuesTransformer extends BaseEnumTransformer
{
    /**
     * Transforms an array of raw values to enumeration objects.
     *
     * @param array $values An array of raw values
     *
     * @return EnumInterface[] An array of EnumInterface instances
     *
     * @throws UnexpectedTypeException       When $values is not array
     * @throws TransformationFailedException When any value is not acceptable for enumeration
     */
    public function reverseTransform($values)
    {
        if (!is_array($values)) {
            throw new UnexpectedTypeException($values, 'array');
        }

        try {
            return array_map(array($this, 'createEnum'), $values);
        } catch (InvalidEnumArgumentException $ex) {
            throw new TransformationFailedException(sprintf(
                'One or more values is not acceptable for enumeration of %s type.', $this->enumClass
            ));
        }
    }

    /**
     * Transforms an array of enumeration objects to a raw values.
     *
     * @param EnumInterface[] $values An array of EnumInterface instances
     *
     * @return array An array of raw values
     *
     * @throws UnexpectedTypeException       When $values is not array or the FlaggedEnum instance
     * @throws TransformationFailedException When any value is not instance of the enumeration
     */
    public function transform($values)
    {
        if ($values === null) {
            return array();
        }

        if (!is_array($values)) {
            throw new UnexpectedTypeException($values, 'array');
        }

        $result = array();
        foreach ($values as $value) {
            if (!$value instanceof $this->enumClass) {
                throw new TransformationFailedException(sprintf(
                    'Could not convert a value of type "%s" to choice, it is to be an instance of %s.',
                    is_object($value) ? get_class($value) : gettype($value), $this->enumClass
                ));
            }
            $result[] = $value->getValue();
        }

        return $result;
    }
}