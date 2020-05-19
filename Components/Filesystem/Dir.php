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

use Chevere\Components\Filesystem\Exceptions\DirNotExistsException;
use Chevere\Components\Filesystem\Exceptions\DirTailException;
use Chevere\Components\Filesystem\Exceptions\DirUnableToCreateException;
use Chevere\Components\Filesystem\Exceptions\DirUnableToRemoveException;
use Chevere\Components\Filesystem\Exceptions\PathIsFileException;
use Chevere\Components\Filesystem\Exceptions\PathIsNotDirectoryException;
use Chevere\Components\Filesystem\File;
use Chevere\Components\Filesystem\Interfaces\DirInterface;
use Chevere\Components\Filesystem\Interfaces\PathInterface;
use Chevere\Components\Filesystem\Path;
use Chevere\Components\Message\Message;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Throwable;

class Dir implements DirInterface
{
    private PathInterface $path;

    /**
     * @throws PathIsFileException if the PathInterface represents a file
     * @throws DirTailException if the path doesn't ends with a trailing slash.
     */
    public function __construct(PathInterface $path)
    {
        $this->path = $path;
        $this->assertIsNotFile();
        $absolute = $path->absolute();
        if ($absolute[-1] !== '/') {
            throw new DirTailException(
                (new Message('Instance of %className% must provide an absolute path ending with %tailChar%, path %provided% provided'))
                    ->code('%className%', get_class($path))
                    ->code('%tailChar%', '/')
                    ->code('%provided%', $absolute)
            );
        }
    }

    public function getChild(string $path): DirInterface
    {
        return new Dir($this->path->getChild($path));
    }

    final public function path(): PathInterface
    {
        return $this->path;
    }

    final public function exists(): bool
    {
        return $this->path->exists() && $this->path->isDir();
    }

    final public function assertExists(): void
    {
        if (!$this->exists()) {
            throw new DirNotExistsException(
                (new Message("File %path% doesn't exists"))
                    ->code('%path%', $this->path->absolute())
            );
        }
    }

    final public function create(int $mode = 0755): void
    {
        try {
            mkdir($this->path->absolute(), $mode, true);
        } catch (Throwable $e) {
            throw new DirUnableToCreateException(
                (new Message('Unable to create directory %path% %thrown%'))
                    ->code('%path%', $this->path->absolute())
                    ->code('%thrown%', '[' . $e->getMessage() . ']')
            );
        }
    }

    final public function remove(): array
    {
        $this->assertIsDir();
        $array = $this->removeContents();
        $this->rmdir();
        $array[] = $this->path->absolute();

        return $array;
    }

    final public function removeContents(): array
    {
        $this->assertIsDir();
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $this->path->absolute(),
                RecursiveDirectoryIterator::SKIP_DOTS
            ),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        $removed = [];
        foreach ($files as $fileInfo) {
            if ($fileInfo->isDir()) {
                $path = new Path(rtrim($fileInfo->getRealPath(), '/') . '/');
                (new Dir($path))->rmdir();
                $removed[] = $path->absolute();
                continue;
            } else {
                $path = new Path($fileInfo->getRealPath());
            }
            (new File($path))->remove();
            $removed[] = $path->absolute();
        }

        return $removed;
    }

    private function assertIsNotFile(): void
    {
        if ($this->path->isFile()) {
            throw new PathIsFileException(
                (new Message('Path %path% is a file'))
                    ->code('%path%', $this->path->absolute())
            );
        }
    }

    /**
     * @throws PathIsNotDirectoryException if the directory doesn't exists
     */
    private function assertIsDir(): void
    {
        if (!$this->path->isDir()) {
            throw new PathIsNotDirectoryException(
                (new Message('Path %path% is not a directory'))
                    ->code('%path%', $this->path->absolute())
            );
        }
    }

    private function rmdir(): void
    {
        try {
            rmdir($this->path->absolute());
        } catch (Throwable $e) {
            throw new DirUnableToRemoveException(
                (new Message('Unable to remove directory %path% %thrown%'))
                    ->code('%path%', $this->path->absolute())
                    ->code('%thrown%', '[' . $e->getMessage() . ']')
            );
        }
    }
}
