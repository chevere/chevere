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

use function Chevere\Components\Iterator\recursiveDirectoryIteratorFor;
use Chevere\Components\Message\Message;
use Chevere\Exceptions\Filesystem\DirExistsException;
use Chevere\Exceptions\Filesystem\DirNotExistsException;
use Chevere\Exceptions\Filesystem\DirUnableToCreateException;
use Chevere\Exceptions\Filesystem\DirUnableToRemoveException;
use Chevere\Exceptions\Filesystem\PathIsFileException;
use Chevere\Exceptions\Filesystem\PathIsNotDirectoryException;
use Chevere\Exceptions\Filesystem\PathTailException;
use Chevere\Interfaces\Filesystem\DirInterface;
use Chevere\Interfaces\Filesystem\PathInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use function Safe\mkdir;
use function Safe\rmdir;
use Throwable;

final class Dir implements DirInterface
{
    public function __construct(
        private PathInterface $path
    ) {
        $this->assertIsNotFile();
        $this->assertTailDir();
    }

    public function getChild(string $path): DirInterface
    {
        return new self($this->path->getChild($path));
    }

    public function path(): PathInterface
    {
        return $this->path;
    }

    public function exists(): bool
    {
        return $this->path->isDir();
    }

    public function assertExists(): void
    {
        if (!$this->exists()) {
            throw new DirNotExistsException(
                (new Message("Dir %path% doesn't exists"))
                    ->code('%path%', $this->path->toString())
            );
        }
    }

    public function create(int $mode = 0755): void
    {
        if ($this->exists()) {
            throw new DirExistsException(
                (new Message('Directory %path% already exists'))
                    ->code('%path%', $this->path->toString())
            );
        }
        $this->assertCreate($mode);
    }

    public function createIfNotExists(int $mode = 0755): void
    {
        if ($this->exists()) {
            return;
        }
        $this->assertCreate($mode);
    }

    public function remove(): array
    {
        $this->assertIsDir();
        $array = $this->removeContents();
        $this->rmdir();
        $array[] = $this->path->toString();

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
                $realPath = $fileInfo->getRealPath();
                $realPath .= (substr($realPath, -1) == DIRECTORY_SEPARATOR ? '' : DIRECTORY_SEPARATOR);
                $path = new Path($realPath);
                (new self($path))->rmdir();
                $removed[] = $path->toString();

                continue;
            }
            $path = new Path($fileInfo->getRealPath());

            (new File($path))->remove();
            $removed[] = $path->toString();
        }

        return $removed;
    }

    private function assertIsNotFile(): void
    {
        if ($this->path->isFile()) {
            throw new PathIsFileException(
                (new Message('Path %path% is a file'))
                    ->code('%path%', $this->path->toString())
            );
        }
    }

    private function assertTailDir(): void
    {
        $absolute = $this->path->toString();
        if ($absolute[-1] !== DIRECTORY_SEPARATOR) {
            throw new PathTailException(
                (new Message('Instance of %className% must provide an absolute path ending with %tailChar%, path %provided% provided'))
                    ->code('%className%', $this->path::class)
                    ->code('%tailChar%', DIRECTORY_SEPARATOR)
                    ->code('%provided%', $absolute)
            );
        }
    }

    private function assertIsDir(): void
    {
        if (!$this->path->isDir()) {
            throw new PathIsNotDirectoryException(
                (new Message('Path %path% is not a directory'))
                    ->code('%path%', $this->path->toString())
            );
        }
    }

    /**
     * @codeCoverageIgnore
     */
    private function assertCreate(int $mode = 0755): void
    {
        try {
            mkdir($this->path->toString(), $mode, true);
        } catch (Throwable $e) {
            throw new DirUnableToCreateException(previous: $e, );
        }
    }

    /**
     * @codeCoverageIgnore
     */
    private function rmdir(): void
    {
        try {
            rmdir($this->path->toString());
        } catch (Throwable $e) {
            throw new DirUnableToRemoveException(previous: $e);
        }
    }
}
