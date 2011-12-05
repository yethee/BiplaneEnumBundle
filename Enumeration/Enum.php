<?php

namespace Biplane\EnumBundle\Enumeration;

use Biplane\EnumBundle\Exception\InvalidEnumArgumentException;

/**
 * Base class of enumeration.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
abstract class Enum implements EnumInterface
{
    protected $value;

    /**
     * The constructor is protected: use the static create method instead.
     *
     * @param mixed $value
     */
    protected function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Creates a new instance of enumeration.
     *
     * @param mixed $value
     * @return EnumInterface
     *
     * @throws InvalidEnumArgumentException
     */
    public static function create($value)
    {
        if (!static::isAcceptableValue($value)) {
            throw new InvalidEnumArgumentException($value);
        }

        return new static($value);
    }

    /**
     * Gets the raw value.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Returns the human representation of the value.
     *
     * @return string
     */
    public function getReadable()
    {
        return static::getReadableFor($this->getValue());
    }

    /**
     * Converts to the human representation of the current value.
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getReadable();
    }

    /**
     * Tells is this value is acceptable.
     *
     * @param mixed $value
     * @return boolean
     */
    public static function isAcceptableValue($value)
    {
        return in_array($value, static::getPossibleValues(), true);
    }

    /**
     * Gets the human representation for a given value.
     *
     * @param mixed $value
     * @return string
     *
     * @throws InvalidEnumArgumentException
     */
    public static function getReadableFor($value)
    {
        if (!static::isAcceptableValue($value)) {
            throw new InvalidEnumArgumentException($value);
        }

        $humanRepresentations = static::getReadables();

        return $humanRepresentations[$value];
    }

    /**
     * Determines whether enums are equals.
     *
     * @param EnumInterface $enum
     *
     * @return bool
     */
    public function equals(EnumInterface $enum)
    {
        return get_class($this) === get_class($enum) &&
            $this->value === $enum->getValue();
    }
}