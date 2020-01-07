<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\Path;

use Chevere\Components\App\Instances\BootstrapInstance;
use Chevere\Components\Message\Message;
use Chevere\Components\Path\Exceptions\PathInvalidException;
use Chevere\Components\Path\Exceptions\PathNotAllowedException;
use Chevere\Contracts\Path\PathAppContract;
use Chevere\Contracts\Path\PathContract;
use function ChevereFn\stringForwardSlashes;
use function ChevereFn\stringReplaceFirst;
use function ChevereFn\stringStartsWith;

/**
 * A proxy class to handle paths in the application context.
 */
class PathApp implements PathContract, PathAppContract
{
    /** @var string Relative path passed on instance construct */
    private string $path;

    /** @var string Path root context */
    private string $root;

    private PathContract $pathContext;

    /** @var string Absolute path */
    private string $absolute;

    /** @var string Relative path (to project root) */
    private string $relative;

    /**
     * {@inheritdoc}
     */
    public function __construct(string $path)
    {
        new CheckFormat($path);
        $this->path = $path;
        $this->root = BootstrapInstance::get()->appPath();
        $this->handlePaths();
        $this->pathContext = new Path($this->absolute);
        $this->setRelativePath();
    }

    /**
     * {@inheritdoc}
     */
    public function absolute(): string
    {
        return $this->pathContext->absolute();
    }

    /**
     * {@inheritdoc}
     */
    public function relative(): string
    {
        return $this->relative;
    }

    /**
     * {@inheritdoc}
     */
    public function exists(): bool
    {
        return $this->pathContext->exists();
    }

    /**
     * {@inheritdoc}
     */
    public function isDir(): bool
    {
        return $this->pathContext->isDir();
    }

    /**
     * {@inheritdoc}
     */
    public function isFile(): bool
    {
        return $this->pathContext->isFile();
    }

    /**
     * {@inheritdoc}
     */
    public function getChild(string $path): PathContract
    {
        return $this->pathContext->getChild($path);
    }

    private function handlePaths(): void
    {
        if (stringStartsWith('/', $this->path)) {
            $this->assertAbsolutePath();
            $this->absolute = $this->path;
        } else {
            $this->assertRelativePath();
            $this->absolute = $this->getAbsolute();
        }
        $this->relative = $this->getRelative();
    }

    private function getAbsolute(): string
    {
        return $this->root . stringForwardSlashes($this->path);
    }

    private function getRelative(): string
    {
        $absolutePath = stringForwardSlashes($this->absolute);

        return stringReplaceFirst($this->root, '', $absolutePath);
    }

    private function assertRelativePath(): void
    {
        if (stringStartsWith('./', $this->path)) {
            throw new PathInvalidException(
                (new Message('Must omit %chars% for the path %path%'))
                    ->code('%chars%', './')
                    ->code('%path%', $this->path)
                    ->toString()
            );
        }
    }

    private function assertAbsolutePath(): void
    {
        if (!stringStartsWith($this->root, $this->path)) {
            throw new PathNotAllowedException(
                (new Message('Only absolute paths in the app path %root% are allowed, path %path% provided'))
                    ->code('%root%', $this->root)
                    ->code('%path%', $this->path)
                    ->toString()
            );
        }
    }

    private function setRelativePath(): void
    {
        $this->relative = stringReplaceFirst($this->root, '', $this->absolute());
    }
}
