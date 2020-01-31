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

use Chevere\Components\Bootstrap\Exceptions\BootstrapException;
use Chevere\Components\Bootstrap\Interfaces\BootstrapInterface;
use Chevere\Components\Console\Interfaces\ConsoleInterface;
use Chevere\Components\Filesystem\Dir\Interfaces\DirInterface;
use Chevere\Components\Filesystem\Path\Interfaces\PathInterface;
use Chevere\Components\Filesystem\Path\Path;
use Chevere\Components\Message\Message;
use Throwable;
use function ChevereFn\stringReplaceFirst;
use function ChevereFn\stringStartsWith;

final class Bootstrap implements BootstrapInterface
{
    private int $time;

    /** @var int High-resolution time (nanoseconds) */
    private int $hrTime;

    /** @var DirInterface Path to the document root (html) */
    private DirInterface $rootDir;

    /** @var DirInterface Path to the application */
    private DirInterface $appDir;

    private ConsoleInterface $console;

    private bool $isCli = false;

    private bool $isConsole = false;

    private bool $isDev = false;

    public function __construct(DirInterface $rootDir, DirInterface $app)
    {
        $this->time = time();
        $this->hrTime = hrtime(true);
        $this->rootDir = $rootDir;
        $this->appDir = $app;
        $this->assertDirExists($rootDir, '$rootDir');
        $this->assertDirExists($app, '$app');
        $this->isCli = false;
        $this->isConsole = false;
        $this->isDev = false;
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

    public function withConsole(ConsoleInterface $console): BootstrapInterface
    {
        $new = clone $this;
        $new->console = $console;

        return $new;
    }

    public function hasConsole(): bool
    {
        return isset($this->console);
    }

    public function console(): ConsoleInterface
    {
        return $this->console;
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

    public function withAppAutoloader(string $namespace): BootstrapInterface
    {
        $new = clone $this;
        $ns = trim($namespace, '\\') . '\\';
        $nsPath = str_replace('\\', '/', $ns);
        spl_autoload_register(function ($className) use ($ns, $nsPath) {
            $matches = stringStartsWith($ns, $className);
            if ($matches) {
                $name = str_replace('\\', '/', $className);
                $path = stringReplaceFirst($nsPath, '', $name) . '.php';
                require $this->rootDir . 'app/src/' . $path;
            }
        });

        return $new;
    }

    private function handleDirectory(DirInterface $dir): void
    {
        try {
            if ($dir->exists() === false) {
                $dir->create();
            }
            if (!$dir->path()->isWriteable()) {
                $dir->path()->chmod(0777);
            }
        } catch (Throwable $e) {
            throw new BootstrapException($e->getMessage());
        }
    }

    private function assertDirExists(DirInterface $dir, string $argumentName): void
    {
        if ($dir->exists() === false) {
            throw new BootstrapException(
                (new Message("Directory %directory% (%argumentName% argument) doesn't exists"))
                    ->code('%directory%', $dir->path()->absolute())
                    ->strong('%argumentName%', $argumentName)
                    ->toString()
            );
        }
    }
}
