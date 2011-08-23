<?php

namespace Biplane\EnumBundle\Tests\Fixtures;

use Biplane\EnumBundle\Enumeration\Enum;

class SimpleEnum extends Enum
{
    const FIRST  = 1;
    const SECOND = 2;

    public static function getReadables()
    {
        return array(
            self::FIRST => 'First',
            self::SECOND => 'Second'
        );
    }

    public static function getPossibleValues()
    {
        return array(self::FIRST, self::SECOND);
    }
}