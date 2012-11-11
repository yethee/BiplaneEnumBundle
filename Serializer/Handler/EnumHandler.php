<?php

namespace Biplane\EnumBundle\Serializer\Handler;

use JMS\SerializerBundle\Serializer\XmlSerializationVisitor;
use JMS\SerializerBundle\Serializer\JsonSerializationVisitor;
use Biplane\EnumBundle\Enumeration\EnumInterface;

/**
 * EnumHandler
 *
 * @author Denis Vasilev <yethee@biplane.ru>
 */
class EnumHandler
{
    /**
     * Serialize the Enum object to json.
     *
     * @param JsonSerializationVisitor $visitor The visitor
     * @param EnumInterface            $data    A EnumInterface instance
     * @param array                    $type    The type parameters
     *
     * @return mixed
     */
    public function serializeEnumToJson(JsonSerializationVisitor $visitor, EnumInterface $data, array $type)
    {
        return $data->getValue();
    }

    /**
     * Serialize the Enum object to xml.
     *
     * @param XmlSerializationVisitor $visitor The visitor
     * @param EnumInterface           $data    A EnumInterface instance
     * @param array                   $type    The type parameters
     *
     * @return \DOMCdataSection
     */
    public function serializeEnumToXml(XmlSerializationVisitor $visitor, EnumInterface $data, array $type)
    {
        if ($visitor->document === null) {
            $visitor->document = $visitor->createDocument(null, null, true);
        }

        return $visitor->document->createCDATASection($data->getValue());
    }
}