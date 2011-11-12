<?php

namespace Biplane\EnumBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use JMS\SerializerBundle\DependencyInjection\JMSSerializerExtension;
use Biplane\EnumBundle\DependencyInjection\Factory\EnumHandlerFactory;

/**
 * Bundle provides support typed enumeration.
 *
 * @author Denis Vasilev <yethee@biplane.ru>
 */
class BiplaneEnumBundle extends Bundle
{
    public function configureSerializerExtension(JMSSerializerExtension $extension)
    {
        $extension->addHandlerFactory(new EnumHandlerFactory());
    }
}