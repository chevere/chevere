<?php

declare(strict_types=1);
/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevereto\Chevere;

use LogicException;

/**
 * ArrayFile provides a object oriented method to interact with array files (return []).
 */
class ArrayFile
{
    /** @const array Type validators, taken from https://www.php.net/manual/en/ref.var.php */
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

    // NOTE: Why these?

    /** @var array */
    protected $array;

    /** @var string */
    protected $filepath;

    /** @var string A type, classname or interface */
    protected $typeSome;

    /** @var string */
    protected $type;
    protected $className;
    protected $interfaceName;

    /**
     * @param string $fileHandle Path handle or absolute filepath
     * @param array  $typeSome   If set, the array members must match the target type, classname or interface
     */
    public function __construct(PathHandle $pathHandle, string $typeSome = null)
    {
        $filepath = $pathHandle->getPath();
        $this->typeSome = $typeSome;
        $fileArray = Load::php($filepath);
        $this->filepath = $filepath;
        $arrayFileType = gettype($fileArray);
        try {
            $this->handleFileArray($fileArray);
            if (null !== $typeSome) {
                $this->handleTypeSome($this->typeSome);
                $this->handleNullType($this->type);
                $this->validate($fileArray);
            }
        } catch (LogicException $e) {
            $message = (string) (new Message($e->getMessage()))
                ->code('%arrayFileType%', $arrayFileType)
                ->code('%filepath%', $filepath)
                ->code('%members%', $this->className ?? $this->interfaceName ?? $this->type)
                ->code('%typeSome%', $typeSome);
            throw new LogicException($message);
        }
        $this->array = $fileArray;
    }

    protected function handleFileArray($fileArray)
    {
        if (!is_array($fileArray)) {
            throw new LogicException('Expecting file %filepath% return type array, %arrayFileType% provided.');
        }
    }

    protected function handleTypeSome($typeSome)
    {
        if (isset(static::TYPE_VALIDATORS[$typeSome])) {
            $this->type = $typeSome;
        } else {
            $this->handleClassAndInterfaceName($typeSome);
            if (null != $this->className || null != $this->interfaceName) {
                $this->type = 'object';
            }
        }
    }

    protected function handleClassAndInterfaceName(string $typeSome)
    {
        if (class_exists($typeSome)) {
            $this->className = $typeSome;
        } elseif (interface_exists($typeSome)) {
            $this->interfaceName = $typeSome;
        }
    }

    protected function handleNullType($type)
    {
        if (null == $type) {
            throw new LogicException('Argument #2 must be a valid data type, class name or interface name. %typeSome% provided.');
        }
    }

    /**
     * Validates array content type.
     */
    protected function validate(array $array): self
    {
        $validator = static::TYPE_VALIDATORS[$this->type];
        foreach ($array as $k => $v) {
            if ($validate = $validator($v)) {
                if ($this->type == 'object') {
                    $validate = $this->getValidateObject($v, $this->className, $this->interfaceName);
                }
            }
            if (false == $validate) {
                $this->handleInvalidation($k, $v);
            }
        }

        return $this;
    }

    protected function getValidateObject(object $object, ?string $className, ?string $interfaceName): bool
    {
        if (null != $className) {
            return get_class($object) == $className;
        } elseif (null != $interfaceName) {
            return $object instanceof $interfaceName;
        }

        return false;
    }

    protected function handleInvalidation($k, $v)
    {
        $type = gettype($v);
        if ($type == 'object') {
            $type .= ' '.get_class($v);
        }
        throw new LogicException(
                (string) (new Message('Expecting array containing only %members% members, %type% found at %filepath% (key %key%).'))
                    ->code('%type%', $type)
                    ->code('%key%', $k)
        );
    }

    public function getFilepath(): string
    {
        return $this->filepath;
    }

    public function getType(): ?string
    {
        return $this->type ?? null;
    }

    public function toArray(): array
    {
        return $this->array ?? [];
    }
}
