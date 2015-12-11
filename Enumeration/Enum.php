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
     * @param mixed $value The raw value of an enumeration
     */
    protected function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * {@inheritdoc}
     */
    public static function create($value)
    {
        if (!static::isAcceptableValue($value)) {
            throw new InvalidEnumArgumentException($value);
        }

        return new static($value);
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public static function isAcceptableValue($value)
    {
        return in_array($value, static::getPossibleValues(), true);
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function equals(EnumInterface $enum)
    {
        return get_class($this) === get_class($enum) && $this->value === $enum->getValue();
    }
}
