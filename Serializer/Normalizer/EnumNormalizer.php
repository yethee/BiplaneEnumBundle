<?php

namespace Biplane\EnumBundle\Serializer\Normalizer;

use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Biplane\EnumBundle\Enumeration\EnumInterface;

/**
 * EnumNormalizer
 *
 * @author Denis Vasilev <yethee@biplane.ru>
 */
class EnumNormalizer extends SerializerAwareNormalizer implements NormalizerInterface, DenormalizerInterface
{
    /**
     * Normalizes an object into a set of arrays/scalars
     *
     * @param object $object object to normalize
     * @param string $format format the normalization result will be encoded as
     *
     * @return mixed
     */
    public function normalize($object, $format = null)
    {
        return $object->getValue();
    }

    /**
     * Denormalizes data back into an object of the given class
     *
     * @param mixed  $data   data to restore
     * @param string $class  the expected class to instantiate
     * @param string $format format the given data was extracted from
     *
     * @return EnumInterface
     */
    public function denormalize($data, $class, $format = null)
    {
        return call_user_func(array($class, 'create'), $data);
    }

    /**
     * Checks whether the given class is supported for normalization by this normalizer
     *
     * @param mixed   $data   Data to normalize.
     * @param string  $format The format being (de-)serialized from or into.
     *
     * @return Boolean
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof EnumInterface;
    }

    /**
     * Checks whether the given class is supported for denormalization by this normalizer
     *
     * @param mixed   $data   Data to denormalize from.
     * @param string  $type   The class to which the data should be denormalized.
     * @param string  $format The format being deserialized from.
     *
     * @return Boolean
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        $reflection = new \ReflectionClass($type);

        if ($reflection->isSubclassOf('Biplane\\EnumBundle\\Enumeration\\EnumInterface')) {
            if (call_user_func(array($type, 'isAcceptableValue'), $data)) {
                return true;
            }
        }

        return false;
    }
}