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
    private static $masks = array();

    protected $flags;

    /**
     * Tells is this value is acceptable.
     *
     * @param mixed $value
     *
     * @return boolean True if $value is acceptable for this enumeration type; otherwise false
     *
     * @throws \InvalidArgumentException When $value is invalid type
     */
    public static function isAcceptableValue($value)
    {
        if (!is_int($value)) {
            throw new \InvalidArgumentException(sprintf(
                'Expected argument of type "integer", "%s" given.',
                is_object($value) ? get_class($value) : gettype($value)
            ));
        }

        if ($value === 0) {
            return parent::isAcceptableValue($value);
        }
        
        return $value === ($value & static::getBitmask());
    }

    /**
     * Gets the human representation for a given value.
     *
     * @param mixed $value The value of a particular enumerated constant
     *
     * @return string The human representation for a given value
     *
     * @throws InvalidEnumArgumentException When $value is not acceptable for this enumeration type
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
     *
     * @throws \UnexpectedValueException
     */
    protected static function getBitmask()
    {
        $enumType = get_called_class();

        if (!isset(self::$masks[$enumType])) {
            $mask = 0;

            foreach (static::getPossibleValues() as $flag) {
                if ($flag === 0) {
                    continue;
                }

                if ($flag < 1 || ($flag > 1 && ($flag % 2) !== 0)) {
                    throw new \UnexpectedValueException(sprintf(
                        'Possible value (%d) of the enumeration is not the bit flag.', $flag
                    ));
                }

                $mask |= $flag;
            }

            self::$masks[$enumType] = $mask;
        }

        return self::$masks[$enumType];
    }

    /**
     * Gets the bitmask of possible values.
     * 
     * @return int
     *
     * @throws \UnexpectedValueException
     *
     * @deprecated
     */
    protected static function getMaskOfPossibleValues()
    {
        $mask = 0;

        foreach (static::getPossibleValues() as $flag) {
            if ($flag > 1 && ($flag % 2) !== 0) {
                throw new \UnexpectedValueException(sprintf(
                    'Possible value (%d) of the enumeration is not the bit flag.', $flag
                ));
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
     * @param int $bitFlag The bit flag or bit flags.
     *
     * @return bool True if the bit flag or bit flags are also set in the current instance; otherwise, false
     */
    public function hasFlag($bitFlag)
    {
        return $bitFlag === ($bitFlag & $this->value);
    }

    /**
     * Adds a bitmask to the value of this instance.
     *
     * Returns a new instance of this enumeration type.
     *
     * @param int $flags The bit flag or bit flags
     *
     * @return EnumInterface A new instance of the enumeration
     *
     * @throws InvalidEnumArgumentException When $flags is not acceptable for this enumeration type
     */
    public function addFlags($flags)
    {
        if (!static::isAcceptableValue($flags)) {
            throw new InvalidEnumArgumentException($flags);
        }

        return static::create($this->value | $flags);
    }

    /**
     * Removes a bitmask from the value of this instance.
     *
     * Returns a new instance of this enumeration type.
     *
     * @param int $flags The bit flag or bit flags
     *
     * @return EnumInterface A new instance of the enumeration
     *
     * @throws InvalidEnumArgumentException When $flags is not acceptable for this enumeration type
     */
    public function removeFlags($flags)
    {
        if (!static::isAcceptableValue($flags)) {
            throw new InvalidEnumArgumentException($flags);
        }

        return static::create($this->value & ~$flags);
    }
}