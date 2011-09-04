<?php

namespace Biplane\EnumBundle\Enumeration;

use Biplane\EnumBundle\Exception\InvalidEnumArgumentException;

/**
 * Base enumeration of bit flags.
 *
 * @author Denis Vasilev <yethee@biplane.ru>
 */
abstract class FlaggedEnum extends Enum
{
    protected $flags;

    /**
     * Tells is this value is acceptable.
     *
     * @param int $value
     * @return boolean
     *
     * @throw \InvalidArgumentException When invalid type of $value.
     */
    public static function isAcceptableValue($value)
    {
        if (!is_int($value)) {
            throw new \InvalidArgumentException(
                sprintf('Expected argument of type "integer", "%s" given.', is_object($value) ? get_class($value) : gettype($value))
            );
        }

        if ($value === 0) {
            return parent::isAcceptableValue($value);
        }
        
        return $value === ($value & static::getBitmask());
    }

    /**
     * Gets the human representation for a given value.
     *
     * @param int $value
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

        if (isset($humanRepresentations[$value])) {
            return $humanRepresentations[$value];
        }

        $parts = array();

        foreach ($humanRepresentations as $flag => $readableValue) {
            if ($flag === ($flag & $value)) {
                $parts[] = $readableValue;
            }
        }

        return implode('; ', $parts);
    }

    /**
     * Gets an integer value of the possible flags for enumeration.
     * 
     * @return int
     */
    protected static function getBitmask()
    {
        throw new \LogicException('This method must be overwritten.');
    }

    /**
     * Gets the bitmask of possible values.
     * 
     * @return int
     *
     * @throws \UnexpectedValueException
     */
    protected static function getMaskOfPossibleValues()
    {
        $mask = 0;

        foreach (static::getPossibleValues() as $flag) {
            if ($flag > 1 && ($flag % 2) !== 0) {
                throw new \UnexpectedValueException(sprintf('Possible value (%d) of the enumeration is not the bit flag.', $flag));
            }

            $mask |= $flag;
        }

        return $mask;
    }

    /**
     * Gets an array of bit flags of the value.
     * 
     * @return array
     */
    public function getFlags()
    {
        if ($this->flags === null) {
            $this->flags = array();

            foreach (static::getPossibleValues() as $flag) {
                if ($this->hasFlag($flag)) {
                    $this->flags[] = $flag;
                }
            }
        }

        return $this->flags;
    }

    /**
     * Determines whether the specified flag is set in a numeric value.
     *
     * @param int $bitFlag
     * @return bool
     */
    public function hasFlag($bitFlag)
    {
        return $bitFlag === ($bitFlag & $this->value);
    }
}