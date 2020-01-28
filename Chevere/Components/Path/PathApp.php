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

namespace Chevere\Components\Path;

use Chevere\Components\App\Instances\BootstrapInstance;
use Chevere\Components\Message\Message;
use Chevere\Components\Path\Exceptions\PathNotAllowedException;
use Chevere\Components\Path\Interfaces\CheckFormatInterface;
use Chevere\Components\Path\Interfaces\PathAppInterface;
use Chevere\Components\Path\Interfaces\PathInterface;
use function ChevereFn\stringForwardSlashes;
use function ChevereFn\stringReplaceFirst;
use function ChevereFn\stringStartsWith;

/**
 * A proxy class to handle paths in the application context.
 */
class PathApp implements PathAppInterface
{
    // private CheckFormatInterface $checkFormat;

    /** @var string Relative path passed on instance construct */
    private string $path;

    /** @var string Path root context */
    private string $root;

    private PathInterface $pathContext;

    /** @var string Absolute path */
    private string $absolute;

    /** @var string Relative path (to project root) */
    private string $relative;

    /**
     * Construct a new instance.
     */
    public function __construct(string $path)
    {
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
    public function getChild(string $path): PathInterface
    {
        return $this->pathContext->getChild($path);
    }

    private function handlePaths(): void
    {
        if (stringStartsWith('/', $this->path)) {
            $this->assertAbsolutePath();
            $this->absolute = $this->path;
        } else {
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
