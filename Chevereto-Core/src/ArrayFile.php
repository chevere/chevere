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
use InvalidArgumentException;

/**
 * ArrayFile provides a object oriented method to interact with array files (return []).
 */
class ArrayFile
{
    /** @var array */
    protected $array;

    /** @var string */
    protected $filepath;

    /**
     * @param string $fileHandle path handle or absolute filepath
     */
    public function __construct(string $fileHandle)
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
        $this->filepath = $filepath;
        $this->array = $array;
    }

    public function getFilepath(): string
    {
        return $this->filepath;
    }

    public function toArray(): array
    {
        return $this->array;
    }
}
