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

use Chevere\Components\Message\Message;
use Chevere\Components\Hooks\Exceptions\HooksClassNotRegisteredException;
use Chevere\Components\Hooks\Exceptions\HooksFileNotFoundException;
use Chevere\Components\Hooks\Interfaces\HookableInterface;
use LogicException;
use RuntimeException;
use Throwable;

/**
 * Provides interaction for registered hooks.
 */
final class Hooks
{
    /** @var array ClassName, */
    private array $classMap;

    public function __construct(array $classMap)
    {
        $this->classMap = $classMap;
    }

    public function has(string $className): bool
    {
        return isset($this->classMap[$className]);
    }

    /**
     *
     * @throws HooksClassNotRegisteredException
     * @throws HooksFileNotFoundException
     * @throws RuntimeException if unable to load the hooks file
     * @throws LogicException if the contents of the hooks file are invalid
     */
    public function queue(string $className): HooksQueue
    {
        $hooksPath = $this->classMap[$className] ?? null;
        if ($hooksPath === null) {
            throw new HooksClassNotRegisteredException(
                (new Message("Class %className% doesn't exists in the class map"))
                    ->code('%className%', $className)
                    ->toString()
            );
        }
        if (stream_resolve_include_path($hooksPath) === false) {
            throw new HooksFileNotFoundException(
                (new Message("File %fileName% doesn't exists"))
                    ->code('%fileName%', $hooksPath)
                    ->toString()
            );
        }
        // @codeCoverageIgnoreStart
        try {
            $hooks = include $hooksPath;
        } catch (Throwable $e) {
            throw new RuntimeException($e->getMessage());
        }
        // @codeCoverageIgnoreEnd
        if (is_array($hooks) === false) {
            throw new LogicException(
                (new Message('Expecting type %expectedType%, type %type% at %fileName% hooks for %className%'))
                    ->code('%expectedType%', 'array')
                    ->code('%type%', gettype($hooks))
                    ->code('%className%', $className)
                    ->code('%fileName%', $hooksPath)
                    ->toString()
            );
        }

        return new HooksQueue($hooks);
    }
}
