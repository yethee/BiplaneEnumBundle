<?php

namespace Biplane\EnumBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Biplane\EnumBundle\Enumeration\EnumInterface;

/**
 * ChoiceToEnumTransformer
 *
 * @author Denis Vasilev <yethee@biplane.ru>
 */
class ChoiceToEnumTransformer implements DataTransformerInterface
{
    /**
     * @var string
     */
    private $enumClass;
    /**
     * @var bool
     */
    private $flaggedEnum;
    /**
     * @var bool
     */
    private $multiple;

    /**
     * @param string $enumClass
     * @param bool $multiple
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($enumClass, $multiple = false)
    {
        $reflection = new \ReflectionClass($enumClass);

        if (!$reflection->implementsInterface('Biplane\EnumBundle\Enumeration\EnumInterface')) {
            throw new \InvalidArgumentException(
                sprintf('Enum class "%s" must be implements of Biplane\EnumBundle\Enumeration\EnumInterface.', $enumClass)
            );
        }

        $this->enumClass = $enumClass;
        $this->multiple = (bool)$multiple;
        $this->flaggedEnum = $reflection->isSubclassOf('Biplane\EnumBundle\Enumeration\FlaggedEnum');
    }

    /**
     * Transforms choices to values of enumeration type.
     *
     * @param mixed $value
     * @return EnumInterface|EnumInterface[] Returns an array enums, enum or null.
     *
     * @throws UnexpectedTypeException  When argument is not valid type.
     * @throws DataTransformerException When cast to enum type is failed.
     */
    public function reverseTransform($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!is_string($value) && !is_array($value)) {
            throw new UnexpectedTypeException($value, 'array or string');
        }

        if (!is_array($value)) {
            $value = array($value);
        }

        $callback = $this->enumClass . '::create';
        $enumValue = null;

        try {
            if ($this->flaggedEnum) {
                if (count($value) === 0) {
                    return null;
                }

                $enumValue = 0;
                foreach ($value as $choice) {
                    if (!is_numeric($choice)) {
                        throw new TransformationFailedException(sprintf('Expected choice "%s" as numeric.', gettype($choice)));
                    }

                    $enumValue |= (int)$choice;
                }

                return call_user_func($callback, $enumValue);
            }
            else {
                $choices = array();
                foreach ($value as $choice) {
                    $enumValue = is_numeric($choice) ? (int)$choice : $choice;
                    $choices[] = call_user_func($callback, $enumValue);
                }

                return !$this->multiple ? $choices[0] : $choices;
            }
        }
        catch (\InvalidArgumentException $ex) {
            throw new TransformationFailedException(sprintf('Invalid cast value "%s" to %s type.', $enumValue, $this->enumClass));
        }
    }

    /**
     * Transforms enum values to choices.
     *
     * @param array|null $value
     * @return mixed
     *
     * @throws UnexpectedTypeException
     * @throws DataTransformerException
     */
    public function transform($value)
    {
        if ($value === null) {
            return $this->multiple ? array() : null;
        }

        if ($this->flaggedEnum || !$this->multiple) {
            if (!$value instanceof $this->enumClass) {
                throw new UnexpectedTypeException($value, $this->enumClass);
            }
        }
        else {
            if (!is_array($value)) {
                throw new UnexpectedTypeException($value, 'array');
            }
        }

        if ($this->flaggedEnum) {
            return $value->getFlags();
        }

        if ($this->multiple) {
            foreach ($value as $i => $enumValue) {
                if (!$enumValue instanceof $this->enumClass) {
                    throw new TransformationFailedException(sprintf('Element of array with index of %s is not instance of %s', $i, $this->enumClass));
                }

                $value[$i] = $enumValue->getValue();
            }

            return $value;
        }
        else {
            return $value->getValue();
        }
    }
}