<?php

namespace Biplane\EnumBundle\Tests\Fixtures;

use Biplane\EnumBundle\Enumeration\FlagEnum as BaseFlagEnum;

/**
 * @author Denis Vasilev <yethee@biplane.ru>
 */
class FlagEnum extends BaseFlagEnum
{
    const FIRST  = 1;
    const SECOND = 2;
    const THIRD  = 4;
    const FOURTH = 16;
    const ALL    = 23;

    public static function getReadables()
    {
        return array(
            self::FIRST  => 'First',
            self::SECOND => 'Second',
            self::THIRD  => 'Third',
            self::FOURTH => 'Fourth'
        );
    }

    public static function getPossibleValues()
    {
        return array(self::FIRST, self::SECOND, self::THIRD, self::FOURTH);
    }

    /**
     * @return int
     */
    protected static function getAllFlagsValue()
    {
        return self::ALL;
    }
}