<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevere.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\Hooks;

use Chevere\Components\Filesystem\File;
use Chevere\Components\Filesystem\Path;
use Chevere\Components\Filesystem\PhpFile;
use Chevere\Components\Filesystem\PhpFileReturn;
use Chevere\Components\Hooks\Exceptions\HooksClassNotRegisteredException;
use Chevere\Components\Hooks\Exceptions\HooksFileNotFoundException;
use Chevere\Components\Message\Message;
use Chevere\Components\Type\Type;
use Ds\Map;
use Exception;
use LogicException;
use RuntimeException;
use Throwable;

final class Hooks
{
    private HookablesMap $map;

    public function __construct(HookablesMap $map)
    {
        $this->map = $map;
    }

    public function has(string $hookable): bool
    {
        return $this->map->has($hookable);
    }

    /**
     * @throws HooksClassNotRegisteredException
     * @throws HooksFileNotFoundException
     * @throws RuntimeException if unable to load the hooks file
     * @throws LogicException if the contents of the hooks file are invalid
     */
    public function getQueue(string $className): HooksQueue
    {
        if (!$this->map->has($className)) {
            throw new HooksClassNotRegisteredException(
                (new Message("Class %className% doesn't exists in the class map"))
                    ->code('%className%', $className)
                    ->toString()
            );
        }
        $hooksPath = $this->map->get($className);
        if (stream_resolve_include_path($hooksPath) === false) {
            throw new HooksFileNotFoundException(
                (new Message("File %fileName% doesn't exists"))
                    ->code('%fileName%', $hooksPath)
                    ->toString()
            );
        }
        // @codeCoverageIgnoreStart
        try {
            $fileReturn = new PhpFileReturn(new PhpFile(new File(new Path($hooksPath))));
            $fileReturn = $fileReturn->withStrict(false);
            /**
             * @var HooksQueue $queue
             */
            $queue = $fileReturn->var();
            if (!(new Type(HooksQueue::class))->validate($queue)) {
                throw new LogicException(
                    (new Message('Return value of %filePath% is not of type %type%'))
                        ->code('%filePath%', $hooksPath)
                        ->code('%type%', HooksQueue::class)
                        ->toString()
                );
            }
        } catch (Throwable $e) {
            throw new RuntimeException($e->getMessage());
        }
        // @codeCoverageIgnoreEnd

        return $queue;
    }
}
