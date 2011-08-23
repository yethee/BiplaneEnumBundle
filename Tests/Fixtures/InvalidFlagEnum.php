<?php

namespace Biplane\EnumBundle\Tests\Fixtures;

use Biplane\EnumBundle\Enumeration\FlagEnum as BaseFlagEnum;

/**
 * @author Denis Vasilev <yethee@biplane.ru>
 */
class InvalidFlagEnum extends BaseFlagEnum
{
    const FIRST   = 1;
    const SECOND  = 2;
    const INVALID = 3;

    public static function getReadables()
    {
        return array(
            self::FIRST   => 'First',
            self::SECOND  => 'Second',
            self::INVALID => 'Invalid'
        );
    }

    public static function getPossibleValues()
    {
        return array(self::FIRST, self::SECOND, self::INVALID);
    }

    /**
     * @return int
     */
    protected static function getAllFlagsValue()
    {
        return self::getMaskOfPossibleValues();
    }
}