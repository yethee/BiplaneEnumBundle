<?php

namespace Biplane\EnumBundle\Tests\Fixtures;

/**
 * @author Denis Vasilev <yethee@biplane.ru>
 */
class FlagsWithZeroEnum extends FlagsEnum
{
    const NONE = 0;

    public static function getPossibleValues()
    {
        $values = parent::getPossibleValues();
        $values[] = self::NONE;

        return $values;
    }

    public static function getReadables()
    {
        $readables = parent::getReadables();
        $readables[self::NONE] = 'None';

        return $readables;
    }
}