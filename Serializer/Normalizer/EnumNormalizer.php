<?php

namespace Biplane\EnumBundle\Serializer\Normalizer;

use Biplane\EnumBundle\Enumeration\EnumInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;

/**
 * EnumNormalizer
 *
 * @author Denis Vasilev <yethee@biplane.ru>
 */
class EnumNormalizer implements NormalizerInterface, DenormalizerInterface
{
    use SerializerAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function normalize($object, $format = null, array $context = array())
    {
        return $object->getValue();
    }

    /**
     * {@inheritDoc}
     */
    public function denormalize($data, $class, $format = null, array $context = array())
    {
        return call_user_func(array($class, 'create'), $data);
    }

    /**
     * {@inheritDoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof EnumInterface;
    }

    /**
     * {@inheritDoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        $reflection = new \ReflectionClass($type);

        if ($reflection->isSubclassOf(EnumInterface::class)) {
            if (call_user_func(array($type, 'isAcceptableValue'), $data)) {
                return true;
            }
        }

        return false;
    }
}
