<?php

namespace Biplane\EnumBundle\Form;

use Symfony\Component\Form\AbstractExtension;

/**
 * EnumExtension
 *
 * @author Denis Vasilev <yethee@biplane.ru>
 */
class EnumExtension extends AbstractExtension
{
    protected function loadTypes()
    {
        return array(
            new Type\EnumType(),
        );
    }
}
