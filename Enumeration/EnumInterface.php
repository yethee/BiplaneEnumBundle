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
     * @param mixed $value
     * @return EnumInterface
     *
     * @throws InvalidEnumArgumentException
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
     * @return boolean
     */
    static function isAcceptableValue($value);

    /**
     * Gets the human representation for a given value.
     *
     * @param mixed $value
     * @return string
     *
     * @throws InvalidEnumArgumentException
     */
    static function getReadableFor($value);

    /**
     * Gets the raw value.
     *
     * @return mixed
     */
    function getValue();

    /**
     * Returns the human representation of the value.
     *
     * @return string
     */
    function getReadable();
}