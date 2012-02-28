<?php

namespace Biplane\EnumBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Biplane\EnumBundle\Enumeration\EnumInterface;

/**
 * BaseEnumTransformer
 *
 * @author Denis Vasilev <yethee@biplane.ru>
 */
abstract class BaseEnumTransformer implements DataTransformerInterface
{
    /**
     * @var string
     */
    protected $enumClass;

    /**
     * Constructor.
     *
     * @param string $enumClass A full class name of enumeration
     *
     * @throws \InvalidArgumentException When $enumClass not implement the EnumInterface
     */
    public function __construct($enumClass)
    {
        $reflection = new \ReflectionClass($enumClass);

        if (!$reflection->implementsInterface('Biplane\EnumBundle\Enumeration\EnumInterface')) {
            throw new \InvalidArgumentException(sprintf(
                'Enum class "%s" must be implements of Biplane\EnumBundle\Enumeration\EnumInterface.', $enumClass
            ));
        }

        $this->enumClass = $reflection->getName();
    }

    /**
     * Creates the enum object for this value.
     *
     * @param mixed $value A raw value
     *
     * @return EnumInterface
     */
    protected function createEnum($value)
    {
        return call_user_func(array($this->enumClass, 'create'), $value);
    }
}