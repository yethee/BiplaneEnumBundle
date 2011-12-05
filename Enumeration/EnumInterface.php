<?php

namespace Biplane\EnumBundle\Enumeration;

use Biplane\EnumBundle\Exception\InvalidEnumArgumentException;

/**
 * Enumeration interface.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Denis Vasilev <yethee@biplane.ru>
 */
interface EnumInterface
{
    /**
     * Instanciates a new enumeration.
     *
     * @param mixed $value The value of a particular enumerated constant
     *
     * @return EnumInterface A new instance of an enum
     *
     * @throws InvalidEnumArgumentException When $value is not acceptable for this enumeration type
     */
    static function create($value);

    /**
     * Gets an array of the possible values.
     *
     * @return array
     */
    static function getPossibleValues();

    /**
     * Gets an array of the human representations indexed by possible values.
     *
     * @return array
     */
    static function getReadables();

    /**
     * Tells is this value is acceptable.
     *
     * @param mixed $value
     *
     * @return boolean True if $value is acceptable for this enumeration type; otherwise false
     */
    static function isAcceptableValue($value);

    /**
     * Gets the human representation for a given value.
     *
     * @param mixed $value The value of a particular enumerated constant
     *
     * @return string The human representation for a given value
     *
     * @throws InvalidEnumArgumentException When $value is not acceptable for this enumeration type
     */
    static function getReadableFor($value);

    /**
     * Gets the raw value.
     *
     * @return mixed
     */
    function getValue();

    /**
     * Gets the human representation of the value.
     *
     * @return string
     */
    function getReadable();

    /**
     * Determines whether enums are equals.
     *
     * @param EnumInterface $enum An enum object to compare with this instance
     *
     * @return bool True if $enum is an enum with the same type and value as this instance; otherwise, false
     */
    function equals(EnumInterface $enum);
}