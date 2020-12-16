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

use Chevere\Components\Message\Message;
use Chevere\Exceptions\Filesystem\DirNotExistsException;
use Chevere\Exceptions\Filesystem\DirTailException;
use Chevere\Exceptions\Filesystem\DirUnableToCreateException;
use Chevere\Exceptions\Filesystem\DirUnableToRemoveException;
use Chevere\Exceptions\Filesystem\PathIsFileException;
use Chevere\Exceptions\Filesystem\PathIsNotDirectoryException;
use Chevere\Interfaces\Filesystem\DirInterface;
use Chevere\Interfaces\Filesystem\PathInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Throwable;
use function Chevere\Components\Iterator\recursiveDirectoryIteratorFor;
use function Safe\mkdir;
use function Safe\rmdir;

final class Dir implements DirInterface
{
    private PathInterface $path;

    public function __construct(PathInterface $path)
    {
        $this->path = $path;
        $this->assertIsNotFile();
        $this->assertTailDir();
    }

    public function getChild(string $path): DirInterface
    {
        return new Dir($this->path->getChild($path));
    }

    public function path(): PathInterface
    {
        return $this->path;
    }

    public function exists(): bool
    {
        return $this->path->exists() && $this->path->isDir();
    }

    public function assertExists(): void
    {
        if (!$this->exists()) {
            throw new DirNotExistsException(
                (new Message("Dir %path% doesn't exists"))
                    ->code('%path%', $this->path->absolute())
            );
        }
    }

    public function create(int $mode = 0755): void
    {
        try {
            mkdir($this->path->absolute(), $mode, true);
        } catch (Throwable $e) {
            throw new DirUnableToCreateException(
                (new Message($e->getMessage())),
            );
        }
    }

    public function remove(): array
    {
        $this->assertIsDir();
        $array = $this->removeContents();
        $this->rmdir();
        $array[] = $this->path->absolute();

        return $array;
    }

    public function removeContents(): array
    {
        $this->assertIsDir();
        $files = new RecursiveIteratorIterator(
            recursiveDirectoryIteratorFor($this, RecursiveDirectoryIterator::SKIP_DOTS),
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

    private function assertTailDir(): void
    {
        $absolute = $this->path->absolute();
        if ($absolute[-1] !== '/') {
            throw new DirTailException(
                (new Message('Instance of %className% must provide an absolute path ending with %tailChar%, path %provided% provided'))
                    ->code('%className%', get_class($this->path))
                    ->code('%tailChar%', '/')
                    ->code('%provided%', $absolute)
            );
        }
    }

    private function assertIsDir(): void
    {
        if (!$this->path->isDir()) {
            throw new PathIsNotDirectoryException(
                (new Message('Path %path% is not a directory'))
                    ->code('%path%', $this->path->absolute())
            );
        }
    }

    /**
     * @codeCoverageIgnore
     */
    private function rmdir(): void
    {
        try {
            rmdir($this->path->absolute());
        } catch (Throwable $e) {
            throw new DirUnableToRemoveException(
                new Message($e->getMessage())
            );
        }
    }
}
