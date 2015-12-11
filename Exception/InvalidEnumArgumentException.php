<?php

namespace Biplane\EnumBundle\Exception;

/**
 * InvalidEnumArgumentException
 *
 * @author Denis Vasilev <yethee@biplane.ru>
 */
class InvalidEnumArgumentException extends \InvalidArgumentException
{
    public function __construct($value)
    {
        parent::__construct(sprintf('"%s" is not an acceptable value.', $value));
    }
}
