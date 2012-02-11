<?php

namespace Biplane\EnumBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Biplane\EnumBundle\Enumeration\EnumInterface;
use Biplane\EnumBundle\Exception\InvalidEnumArgumentException;

/**
 * Transforms between a raw value and an enumeration instance.
 *
 * @author Denis Vasilev <yethee@biplane.ru>
 */
class ValueToEnumTransformer implements DataTransformerInterface
{
    /**
     * @var string
     */
    protected $enumClass;

    /**
     * Constructor.
     *
     * @param string $enumClass A full class name of enumeration
     *
     * @throws \InvalidArgumentException When $enumClass not implement the EnumInterface
     */
    public function __construct($enumClass)
    {
        $reflection = new \ReflectionClass($enumClass);

        if (!$reflection->implementsInterface('Biplane\EnumBundle\Enumeration\EnumInterface')) {
            throw new \InvalidArgumentException(sprintf(
                'Enum class "%s" must be implements of Biplane\EnumBundle\Enumeration\EnumInterface.', $enumClass
            ));
        }

        $this->enumClass = $reflection->getName();
    }

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
                'The value "%s" is not acceptable for enumeration of %s type.', $value, $this->enumClass
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

    /**
     * Creates the enum object for this value.
     *
     * @param mixed $value A raw value
     *
     * @return EnumInterface
     */
    protected function createEnum($value)
    {
        return call_user_func(array($this->enumClass, 'create'), $value);
    }
}