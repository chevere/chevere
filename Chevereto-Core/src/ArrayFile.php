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

use Exception;
use LogicException;
use InvalidArgumentException;

// TODO: ArrayFileObject($fh, 'className')? ArrayFile($fh, 'int')

/**
 * ArrayFile provides a object oriented method to interact with array files (return []).
 */
class ArrayFile
{
    /** @var array */
    protected $array;

    /** @var string */
    protected $filepath;

    /** @var string */
    protected $type;

    /**
     * @param string $fileHandle Path handle or absolute filepath
     * @param array  $className  if passed, the array must contain $className objects
     */
    public function __construct(string $fileHandle, string $className = null)
    {
        try {
            $filepath = Path::fromHandle($fileHandle);
            $array = Load::php($filepath);
        } catch (ErrorException | Exception $e) {
            throw new InvalidArgumentException(
                (string) (new Message('Unable to locate file specified by %s (resolved as %f).'))
                    ->code('%s', $fileHandle)
                    ->code('%f', $filepath)
            );
        }
        $type = gettype($array);
        if (false == is_array($array)) {
            throw new LogicException(
                (string) (new Message('Expecting file %f return type %a, %t provided.'))
                    ->code('%a', 'array')
                    ->code('%t', $type)
                    ->code('%f', $filepath)
            );
        }
        if (null != $className) {
            // TODO: Check if $className is a real class name?
            foreach ($array as $k => $v) {
                $vClassName = null;
                $vType = gettype($v);
                if ($vType == 'object') {
                    $vClassName = get_class($v);
                }
                if ($vClassName == null || $vClassName != $className) {
                    throw new LogicException(
                        (string) (new Message('Expecting array containing only %s members, %t found at %f (key %k).'))
                            ->code('%s', $className)
                            ->code('%t', $vClassName ?? $vType)
                            ->code('%f', $filepath)
                            ->code('%k', $k)
                    );
                }
            }
        }
        $this->type = $className;
        $this->filepath = $filepath;
        $this->array = $array;
    }

    public function getFilepath(): string
    {
        return $this->filepath;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function toArray(): array
    {
        return $this->array;
    }
}
