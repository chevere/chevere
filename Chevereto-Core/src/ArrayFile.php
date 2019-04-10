<?php

declare(strict_types=1);
/*
 * This file is part of Chevereto\Core.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevereto\Core;

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
    public function __construct(string $fileHandle, string $typeSome = null)
    {
        $filepath = Path::fromHandle($fileHandle);
        $arrayFile = Load::php($filepath);
        $this->filepath = $filepath;
        $arrayFileType = gettype($arrayFile);
        if (!is_array($arrayFile)) {
            throw new LogicException(
                (string) (new Message('Expecting file %f return type %a, %t provided.'))
                    ->code('%a', 'array')
                    ->code('%t', $arrayFileType)
                    ->code('%f', $filepath)
            );
        }
        if (null !== $typeSome) {
            if (isset(static::TYPE_VALIDATORS[$typeSome])) {
                $this->type = $typeSome;
            } else {
                if (class_exists($typeSome)) {
                    $this->className = $typeSome;
                } elseif (interface_exists($typeSome)) {
                    $this->interfaceName = $typeSome;
                }
                if (null != $this->className || null != $this->interfaceName) {
                    $this->type = 'object';
                }
            }
            if (null == $this->type) {
                throw new LogicException(
                    (string) (new Message('Argument #2 must be a valid data type, class name or interface name. %s provided.'))
                        ->code('%a', '$typeSome')
                        ->code('%s', $typeSome)
                );
            }
            $this->validate($arrayFile);
        }
        $this->array = $arrayFile;
    }

    /**
     * Validates array content type.
     *
     * @param string $fileHandle Path handle or absolute filepath
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
