<?php

namespace Biplane\EnumBundle\Tests\Serializer\Handler;

class JsonSerializationTest extends BaseSerializationTest
{
    protected function getContent($key)
    {
        static $outputs = array();

        if (!$outputs) {
            $outputs['enum'] = '2';
            $outputs['array_enums'] = '[1,2]';
        }

        if (!isset($outputs[$key])) {
            throw new \RuntimeException(sprintf('The key "%s" is not supported.', $key));
        }

        return $outputs[$key];
    }

    protected function getFormat(): string
    {
        return 'json';
    }
}
