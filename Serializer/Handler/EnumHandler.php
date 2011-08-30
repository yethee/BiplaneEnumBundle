<?php

namespace Biplane\EnumBundle\Serializer\Handler;

use JMS\SerializerBundle\Serializer\Handler\SerializationHandlerInterface;
use JMS\SerializerBundle\Serializer\GenericSerializationVisitor;
use JMS\SerializerBundle\Serializer\XmlSerializationVisitor;
use JMS\SerializerBundle\Serializer\VisitorInterface;
use Biplane\EnumBundle\Enumeration\EnumInterface;

/**
 * EnumHandler
 *
 * @author Denis Vasilev <yethee@biplane.ru>
 */
class EnumHandler implements SerializationHandlerInterface
{
    /**
     * @param VisitorInterface $visitor
     * @param mixed $data
     * @param string $type
     * @param bool $handled
     * @return mixed
     */
    function serialize(VisitorInterface $visitor, $data, $type, &$handled)
    {
        if ($data instanceof EnumInterface) {
            $handled = true;

            if ($visitor instanceof XmlSerializationVisitor) {
                if ($visitor->document === null) {
                    $visitor->document = $visitor->createDocument();
                    $visitor->getCurrentNode()->appendChild($visitor->document->createCDATASection($data->getValue()));
                }
                else {
                    return $visitor->document->createCDATASection($data->getValue());
                }
            }
            else if ($visitor instanceof GenericSerializationVisitor && $visitor->getRoot() === null) {
                $visitor->setRoot($data->getValue());

                return;
            }

            return $data->getValue();
        }

        return;
    }
}