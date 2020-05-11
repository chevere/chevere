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

namespace Chevere\Components\Plugs;

use Chevere\Components\ClassMap\ClassMap;
use Chevere\Components\Filesystem\FileFromString;
use Chevere\Components\Filesystem\FilePhp;
use Chevere\Components\Filesystem\FilePhpReturn;
use Chevere\Components\Message\Message;
use Chevere\Components\Plugs\Exceptions\PlugableNotRegisteredException;
use Chevere\Components\Plugs\Exceptions\PlugsFileNoExistsException;
use Chevere\Components\Plugs\PlugsQueue;
use LogicException;
use RuntimeException;
use Throwable;
use TypeError;

final class Plugs
{
    private ClassMap $plugablesToPlugs;

    private PlugsQueue $plugsQueue;

    public function __construct(ClassMap $classMap)
    {
        $this->plugablesToPlugs = $classMap;
    }

    public function has(string $plugable): bool
    {
        return $this->plugablesToPlugs->has($plugable);
    }

    /**
     * @throws PlugableNotRegisteredException
     * @throws PlugsFileNoExistsException
     * @throws RuntimeException if unable to load the hooks file
     * @throws LogicException if the contents of the hooks file are invalid
     */
    public function getQueue(string $plugable): PlugsQueue
    {
        if (!$this->has($plugable)) {
            throw new PlugableNotRegisteredException(
                (new Message("Class %className% doesn't exists in the class map"))
                    ->code('%className%', $plugable)
            );
        }
        $plugsPath = $this->plugablesToPlugs->get($plugable);
        if (stream_resolve_include_path($plugsPath) === false) {
            throw new PlugsFileNoExistsException(
                (new Message("File %fileName% doesn't exists"))
                    ->code('%fileName%', $plugsPath)
            );
        }
        // @codeCoverageIgnoreStart
        try {
            $fileReturn = new FilePhpReturn(new FilePhp(new FileFromString($plugsPath)));
            $fileReturn = $fileReturn->withStrict(false);
            try {
                $this->plugsQueue = $fileReturn->var();
            } catch (TypeError $e) {
                throw new LogicException(
                    (new Message('Return of %filePath% is not of type %type%'))
                        ->code('%filePath%', $plugsPath)
                        ->code('%type%', PlugsQueue::class)
                        ->toString()
                );
            }
        } catch (Throwable $e) {
            throw new RuntimeException($e->getMessage());
        }
        // @codeCoverageIgnoreEnd

        return $this->plugsQueue;
    }
}
