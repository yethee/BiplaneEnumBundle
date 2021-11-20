<?php

namespace Biplane\EnumBundle\Serializer\Handler;

use Biplane\EnumBundle\Enumeration\EnumInterface;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\XmlSerializationVisitor;

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
        $valueNode = $visitor->getDocument()->createCDATASection($data->getValue());

        $currentNode = $visitor->getCurrentNode();

        if ($currentNode !== null) {
            $currentNode->appendChild($valueNode);
        } else {
            $visitor->setCurrentNode($valueNode);
        }

        return $valueNode;
    }
}
