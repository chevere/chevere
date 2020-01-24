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
use function ChevereFn\stringReplaceFirst;
use function ChevereFn\stringStartsWith;

final class Bootstrap implements BootstrapInterface
{
    private int $time;

    /** @var int High-resolution time (nanoseconds) */
    private int $hrTime;

    /** @var string Path to the document root (html) */
    private string $documentRoot;

    /** @var string Path to the project root */
    private string $rootPath;

    /** @var string Path to the application $rootPath/app/ */
    private string $appPath;

    private bool $isCli;

    private ConsoleInterface $console;

    private bool $isDev;

    public function __construct(string $documentRoot)
    {
        $this->time = time();
        $this->hrTime = hrtime(true);
        $this->documentRoot = $documentRoot;
        $this->assertDocumentRoot();
        $this->rootPath = rtrim(str_replace('\\', '/', $this->documentRoot), '/') . '/';
        $this->appPath = $this->rootPath . 'app/';
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

    public function documentRoot(): string
    {
        return $this->documentRoot;
    }

    public function rootPath(): string
    {
        return $this->rootPath;
    }

    public function appPath(): string
    {
        return $this->appPath;
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
                require $this->documentRoot . 'app/src/' . $path;
            }
        });

        return $new;
    }

    private function assertDocumentRoot(): void
    {
        if (false === stream_resolve_include_path($this->documentRoot)) {
            throw new BootstrapException(sprintf("Specified path for document root %s doesn't exists.", $this->documentRoot));
        }
    }
}
