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

namespace Chevere\Components\Bootstrap;

use Chevere\Components\Message\Message;
use Chevere\Exceptions\Bootstrap\BootstrapDirException;
use Chevere\Exceptions\Core\Exception;
use Chevere\Interfaces\Bootstrap\BootstrapInterface;
use Chevere\Interfaces\Filesystem\DirInterface;
use Throwable;

final class Bootstrap implements BootstrapInterface
{
    private int $time;

    /** @var int High-resolution time (nanoseconds) */
    private int $hrTime;

    /** @var DirInterface Path to the document root (html) */
    private DirInterface $dir;

    private bool $isCli = false;

    public function __construct(DirInterface $dir)
    {
        $this->time = time();
        $this->hrTime = hrtime(true);
        $this->handleDirectory($dir, '$dir');
        $this->dir = $dir;
    }

    public function time(): int
    {
        return $this->time;
    }

    public function hrTime(): int
    {
        return $this->hrTime;
    }

    public function dir(): DirInterface
    {
        return $this->dir;
    }

    public function withCli(bool $bool): BootstrapInterface
    {
        $new = clone $this;
        $new->isCli = $bool;

        return $new;
    }

    public function isCli(): bool
    {
        return $this->isCli;
    }

    private function handleDirectory(DirInterface $dir, string $argumentName): void
    {
        try {
            if ($dir->exists() === false) {
                throw new Exception(
                    (new Message("Directory %directory% (%argumentName% argument) doesn't exists"))
                        ->code('%directory%', $dir->path()->absolute())
                        ->strong('%argumentName%', $argumentName)
                );
            }
            if ($dir->path()->isWritable() === false) {
                $dir->path()->chmod(0777); // @codeCoverageIgnore
            }
        } catch (Throwable $e) {
            throw new BootstrapDirException(
                $e instanceof Exception
                    ? $e->message()
                    : new Message($e->getMessage())
            );
        }
    }
}
