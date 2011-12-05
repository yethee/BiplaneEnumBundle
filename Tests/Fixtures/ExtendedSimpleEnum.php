<?php

namespace Biplane\EnumBundle\Tests\Fixtures;

use Biplane\EnumBundle\Enumeration\Enum;

class ExtendedSimpleEnum extends SimpleEnum
{
    const ZERO   = 0;
    const FIRST  = 1;
    const SECOND = 2;

    public static function getReadables()
    {
        return array(
            self::ZERO => 'Zero',
            self::FIRST => 'First',
            self::SECOND => 'Second'
        );
    }

    public static function getPossibleValues()
    {
        return array(self::ZERO, self::FIRST, self::SECOND);
    }
}