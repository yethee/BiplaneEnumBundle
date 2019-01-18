<?php

namespace Biplane\EnumBundle\Form\DataTransformer;

use Biplane\EnumBundle\Enumeration\EnumInterface;
use Symfony\Component\Form\DataTransformerInterface;

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

        if (!$reflection->implementsInterface(EnumInterface::class)) {
            throw new \InvalidArgumentException(sprintf(
                'Enum class "%s" must be implements of %s',
                $enumClass,
                EnumInterface::class
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
