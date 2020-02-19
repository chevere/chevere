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

namespace Chevere\Components\Filesystem;

use Chevere\Components\Instances\BootstrapInstance;
use Chevere\Components\Filesystem\Interfaces\Dir\DirInterface;
use Chevere\Components\Message\Message;
use Chevere\Components\Filesystem\Exceptions\Path\PathNotAllowedException;
use Chevere\Components\Filesystem\Interfaces\Path\AppPathInterface;
use Chevere\Components\Filesystem\Interfaces\Path\PathInterface;
use Chevere\Components\Str\Str;
use Chevere\Components\Str\StrBool;

/**
 * A proxy class to handle paths in the application context.
 */
class AppPath implements AppPathInterface
{
    // private CheckFormatInterface $checkFormat;

    /** @var string Relative path passed on instance construct */
    private string $path;

    /** @var DirInterface Root dir context */
    private DirInterface $rootDir;

    private PathInterface $pathContext;

    /** @var string Relative path (to project root) */
    private string $relative;

    /**
     * Construct a new instance.
     */
    public function __construct(string $path)
    {
        $this->path = $path;
        $this->relative = $path;
        $this->rootDir = BootstrapInstance::get()->appDir();
        $this->handleRelative();
        $this->pathContext = $this->rootDir->path()->getChild($this->relative);
    }

    public function absolute(): string
    {
        return $this->pathContext->absolute();
    }

    public function relative(): string
    {
        return $this->relative;
    }

    /**
     * @codeCoverageIgnore
     */
    public function exists(): bool
    {
        return $this->pathContext->exists();
    }

    /**
     * @codeCoverageIgnore
     */
    public function isDir(): bool
    {
        return $this->pathContext->isDir();
    }

    /**
     * @codeCoverageIgnore
     */
    public function isFile(): bool
    {
        return $this->pathContext->isFile();
    }

    /**
     * @codeCoverageIgnore
     */
    public function chmod(int $mode): void
    {
        $this->pathContext->chmod($mode);
    }

    /**
     * @codeCoverageIgnore
     */
    public function isWriteable(): bool
    {
        return $this->pathContext->isWriteable();
    }

    /**
     * @codeCoverageIgnore
     */
    public function isReadable(): bool
    {
        return $this->pathContext->isReadable();
    }

    /**
     * @codeCoverageIgnore
     */
    public function getChild(string $path): PathInterface
    {
        return $this->pathContext->getChild($path);
    }

    private function handleRelative(): void
    {
        if ((new StrBool($this->path))->startsWith('/') === true) {
            $this->assertAbsolutePath();
            $string = (string) (new Str($this->path))->replaceFirst($this->rootDir->path()->absolute(), '');
            $this->relative = ltrim($string, '/');
        }
    }

    private function assertAbsolutePath(): void
    {
        if ((new StrBool($this->path))->startsWith($this->rootDir->path()->absolute()) === false) {
            throw new PathNotAllowedException(
                (new Message('Only absolute paths in the app path %root% are allowed, path %path% provided'))
                    ->code('%root%', $this->rootDir->path()->absolute())
                    ->code('%path%', $this->path)
                    ->toString()
            );
        }
    }
}
