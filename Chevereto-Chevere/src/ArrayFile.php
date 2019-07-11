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
            if (null !== $this->typeSome) {
                if (isset(static::TYPE_VALIDATORS[$this->typeSome])) {
                    $this->type = $this->typeSome;
                } else {
                    $this->handleClassAndInterfaceName($this->typeSome);
                    if (null != $this->className || null != $this->interfaceName) {
                        $this->type = 'object';
                    }
                }
                $this->handleNullType($this->type);
                $this->validate($fileArray);
            }
        } catch (LogicException $e) {
            $message = (string) (new Message($e->getMessage()))
                ->code('%arrayFileType%', $arrayFileType)
                ->code('%filepath%', $filepath)
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
            $validate = $validator($v);
            // First layer of validation (type)
            if ($validate) {
                // Do another validation for objects
                if ($this->type == 'object') {
                    if (null != $this->className) {
                        $validate = get_class($v) == $this->className;
                    } elseif (null != $this->interfaceName) {
                        $validate = $v instanceof $this->interfaceName;
                    }
                }
            }
            if (false == $validate) {
                $type = gettype($v);
                if ($type == 'object') {
                    $type .= ' '.get_class($v);
                }
                throw new LogicException(
                    (string) (new Message('Expecting array containing only %w members, %s found at %f (key %k).'))
                        ->code('%w', $this->className ?? $this->interfaceName ?? $this->type)
                        ->code('%s', $type)
                        ->code('%f', $this->filepath)
                        ->code('%k', $k)
                );
            }
        }

        return $this;
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
