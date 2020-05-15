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

use Chevere\Components\Bootstrap\Exceptions\BootstrapDirException;
use Chevere\Components\Bootstrap\Interfaces\BootstrapInterface;
use Chevere\Components\Exception\Exception;
use Chevere\Components\Filesystem\Interfaces\DirInterface;
use Chevere\Components\Message\Message;
use Throwable;

final class Bootstrap implements BootstrapInterface
{
    private int $time;

    /** @var int High-resolution time (nanoseconds) */
    private int $hrTime;

    /** @var DirInterface Path to the document root (html) */
    private DirInterface $rootDir;

    /** @var DirInterface Path to the application */
    private DirInterface $appDir;

    private bool $isCli = false;

    private bool $isDev = false;

    public function __construct(DirInterface $rootDir, DirInterface $app)
    {
        $this->time = time();
        $this->hrTime = hrtime(true);
        $this->handleDirectory($rootDir, '$rootDir');
        $this->handleDirectory($app, '$app');
        $this->rootDir = $rootDir;
        $this->appDir = $app;
    }

    public function time(): int
    {
        return $this->time;
    }

    public function hrTime(): int
    {
        return $this->hrTime;
    }

    public function rootDir(): DirInterface
    {
        return $this->rootDir;
    }

    public function appDir(): DirInterface
    {
        return $this->appDir;
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

    public function withDev(bool $bool): BootstrapInterface
    {
        $new = clone $this;
        $new->isDev = $bool;

        return $new;
    }

    public function isDev(): bool
    {
        return $this->isDev;
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
