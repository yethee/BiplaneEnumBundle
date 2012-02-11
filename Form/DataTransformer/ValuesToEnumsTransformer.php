<?php

namespace Biplane\EnumBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Biplane\EnumBundle\Enumeration\EnumInterface;
use Biplane\EnumBundle\Exception\InvalidEnumArgumentException;

/**
 * Transforms between raw values and enumeration instances.
 *
 * @author Denis Vasilev <yethee@biplane.ru>
 */
class ValuesToEnumsTransformer extends ValueToEnumTransformer
{
    /**
     * @var bool
     */
    private $flaggedEnum;

    /**
     * Constructor.
     *
     * @param string $enumClass A full class name of enumeration
     *
     * @throws \InvalidArgumentException When $enumClass not implement the EnumInterface
     */
    public function __construct($enumClass)
    {
        parent::__construct($enumClass);

        $this->flaggedEnum = is_subclass_of($enumClass, 'Biplane\EnumBundle\Enumeration\FlaggedEnum');
    }

    /**
     * Transforms an array of raw values to enumeration objects.
     *
     * @param array $values An array of raw values
     *
     * @return EnumInterface|EnumInterface[] An array of EnumInterface instance or EnumInterface
     *                                       instance, for flagged enum case
     *
     * @throws UnexpectedTypeException       When $values is not array
     * @throws TransformationFailedException When any value is not the integer type, case of flagged enum
     * @throws TransformationFailedException When any value is not acceptable for enumeration
     */
    public function reverseTransform($values)
    {
        if (!is_array($values)) {
            throw new UnexpectedTypeException($values, 'array');
        }

        if ($this->flaggedEnum) {
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
        } else {
            try {
                return array_map(array($this, 'createEnum'), $values);
            } catch (InvalidEnumArgumentException $ex) {
                throw new TransformationFailedException(sprintf(
                    'One or more values is not acceptable for enumeration of %s type.', $this->enumClass
                ));
            }
        }
    }

    /**
     * Transforms an array of enumeration objects to a raw values.
     *
     * @param EnumInterface|EnumInterface[] $values An array of EnumInterface instance or EnumInterface
     *                                              instance, for flagged enum case
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

        if ($this->flaggedEnum && !$values instanceof $this->enumClass) {
            throw new UnexpectedTypeException($values, $this->enumClass);
        } else if (!$this->flaggedEnum && !is_array($values)) {
            throw new UnexpectedTypeException($values, 'array');
        }

        if ($this->flaggedEnum) {
            return $values->getFlags();
        }

        $result = array();
        foreach ($values as $value) {
            if (!$value instanceof $this->enumClass) {
                throw new TransformationFailedException(sprintf(
                    'Value "%s" is not instance of %s.',
                    is_object($value) ? get_class($value) : gettype($value), $this->enumClass
                ));
            }
            $result[] = $value->getValue();
        }

        return $result;
    }
}