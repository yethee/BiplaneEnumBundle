<?php

namespace Biplane\EnumBundle\Tests\Serializer\Handler;

class XmlSerializationTest extends BaseSerializationTest
{
    protected function getContent($key)
    {
        if (!file_exists($file = __DIR__.'/xml/'.$key.'.xml')) {
            throw new \InvalidArgumentException(sprintf('The key "%s" is not supported.', $key));
        }

        return file_get_contents($file);
    }

    protected function getFormat(): string
    {
        return 'xml';
    }
}
