<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\Type;

/**
 * Type provides type validation toolchain
 */
final class Type
{
    /** @const array Type validators [primitive => validator], taken from https://www.php.net/manual/en/ref.var.php */
    const TYPE_VALIDATORS = [
        'array' => 'is_array',
        'bool' => 'is_bool',
        'callable' => 'is_callable',
        'countable' => 'is_countable',
        'double' => 'is_double',
        'float' => 'is_float',
        'int' => 'is_int',
        'integer' => 'is_integer',
        'iterable' => 'is_iterable',
        'long' => 'is_long',
        'null' => 'is_null',
        'numeric' => 'is_numeric',
        'object' => 'is_object',
        'real' => 'is_real',
        'resource' => 'is_resource',
        'scalar' => 'is_scalar',
        'string' => 'is_string',
    ];

    /** @var string The passed argument in construct */
    private $typeSome;

    /** @var string The dectected primitive type */
    private $primitive;

    /** @var string The detected class name (if any) */
    private $className;

    /** @var string The detected interface name (if any) */
    private $interfaceName;

    /**
     * @var string a primitive type, class name or interface
     */
    public function __construct(string $typeSome)
    {
        $this->typeSome = $typeSome;
        $this->setPrimitive();
    }

    public function typeString(): string
    {
        return $this->className ?? $this->interfaceName ?? $this->primitive;
    }

    public function primitive(): string
    {
        return $this->primitive;
    }

    public function className(): string
    {
        return $this->className;
    }

    public function interfaceName(): string
    {
        return $this->interfaceName;
    }

    public function validate(object $object): bool
    {
        $objectClass = get_class($object);
        switch (true) {
            case isset($this->className, $this->interfaceName):
                return $this->isClassName($objectClass) || $this->isInterfaceInstance($object);
            case isset($this->className):
                return $this->isClassName($objectClass);
            case isset($this->interfaceName):
                return $this->isInterfaceInstance($object);
        }

        return false;
    }

    public function validatePrimitive($var): bool
    {
        return gettype($var) == $this->primitive;
    }

    public function validator(): callable
    {
        return static::TYPE_VALIDATORS[$this->primitive];
    }

    private function isClassName(string $objectClass): bool
    {
        return $objectClass == $this->className;
    }

    private function isInterfaceInstance(object $object): bool
    {
        return $object instanceof $this->interfaceName;
    }

    private function setPrimitive(): void
    {
        if (isset(static::TYPE_VALIDATORS[$this->typeSome])) {
            $this->primitive = $this->typeSome;
            return;
        }
        $this->handleClassName();
        $this->handleInterfaceName();
        if (isset($this->className) || isset($this->interfaceName)) {
            $this->primitive = 'object';
        }
    }

    private function handleClassName(): void
    {
        if (class_exists($this->typeSome)) {
            $this->className = $this->typeSome;
        }
    }

    private function handleInterfaceName(): void
    {
        if (interface_exists($this->typeSome)) {
            $this->interfaceName = $this->typeSome;
        }
    }
}
