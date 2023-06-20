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

namespace Chevere\Filesystem;

use Chevere\Filesystem\Exceptions\DirectoryExistsException;
use Chevere\Filesystem\Exceptions\DirectoryNotExistsException;
use Chevere\Filesystem\Exceptions\DirectoryUnableToCreateException;
use Chevere\Filesystem\Exceptions\DirectoryUnableToRemoveException;
use Chevere\Filesystem\Exceptions\PathIsFileException;
use Chevere\Filesystem\Exceptions\PathIsNotDirectoryException;
use Chevere\Filesystem\Exceptions\PathTailException;
use Chevere\Filesystem\Interfaces\DirectoryInterface;
use Chevere\Filesystem\Interfaces\PathInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Throwable;
use function Chevere\Iterator\recursiveDirectoryIteratorFor;
use function Chevere\Message\message;
use function Safe\mkdir;
use function Safe\rmdir;

final class Directory implements DirectoryInterface
{
    public function __construct(
        private PathInterface $path
    ) {
        $this->assertIsNotFile();
        $this->assertPathTail();
    }

    public function getChild(string $path): DirectoryInterface
    {
        return new self($this->path->getChild($path));
    }

    public function path(): PathInterface
    {
        return $this->path;
    }

    public function exists(): bool
    {
        return $this->path->isDirectory();
    }

    public function assertExists(): void
    {
        if (! $this->exists()) {
            throw new DirectoryNotExistsException(
                message("Directory %path% doesn't exists")
                    ->withCode('%path%', $this->path->__toString())
            );
        }
    }

    // @infection-ignore-all
    public function create(int $mode = 0755): void
    {
        if ($this->exists()) {
            throw new DirectoryExistsException(
                message('Directory %path% already exists')
                    ->withCode('%path%', $this->path->__toString())
            );
        }
        $this->assertCreate($mode);
    }

    // @infection-ignore-all
    public function createIfNotExists(int $mode = 0755): void
    {
        if ($this->exists()) {
            return;
        }
        $this->assertCreate($mode);
    }

    public function remove(): array
    {
        $array = $this->removeContents();
        $this->rmdir();
        $array[] = $this->path->__toString();

        return $array;
    }

    public function removeIfExists(): array
    {
        if (! $this->exists()) {
            return [];
        }

        return $this->remove();
    }

    public function removeContents(): array
    {
        $this->assertIsDirectory();
        $files = new RecursiveIteratorIterator(
            recursiveDirectoryIteratorFor($this, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        $removed = [];
        /** @var SplFileInfo $fileInfo */
        foreach ($files as $fileInfo) {
            if ($fileInfo->isDir()) {
                $realPath = tailDirectoryPath($fileInfo->getRealPath());
                $path = new Path($realPath);
                (new self($path))->rmdir();
                $removed[] = $path->__toString();

                /** @infection-ignore-all */
                continue;
            }
            $path = new Path($fileInfo->getRealPath());

            (new File($path))->remove();
            $removed[] = $path->__toString();
        }

        return $removed;
    }

    private function assertIsNotFile(): void
    {
        if ($this->path->isFile()) {
            throw new PathIsFileException(
                message('Path %path% is a file')
                    ->withCode('%path%', $this->path->__toString())
            );
        }
    }

    private function assertPathTail(): void
    {
        $absolute = $this->path->__toString();
        if ($absolute[-1] !== '/') {
            throw new PathTailException(
                message('Instance of %className% must provide an absolute path ending with %tailChar% path %provided% provided')
                    ->withCode('%className%', $this->path::class)
                    ->withCode('%tailChar%', '/')
                    ->withCode('%provided%', $absolute)
            );
        }
    }

    private function assertIsDirectory(): void
    {
        if (! $this->path->isDirectory()) {
            throw new PathIsNotDirectoryException(
                message('Path %path% is not a directory')
                    ->withCode('%path%', $this->path->__toString())
            );
        }
    }

    /**
     * @codeCoverageIgnore
     * @infection-ignore-all
     */
    private function assertCreate(int $mode = 0755): void
    {
        try {
            mkdir($this->path->__toString(), $mode, true);
        } catch (Throwable $e) {
            throw new DirectoryUnableToCreateException(previous: $e);
        }
    }

    /**
     * @codeCoverageIgnore
     * @infection-ignore-all
     */
    private function rmdir(): void
    {
        try {
            rmdir($this->path->__toString());
        } catch (Throwable $e) {
            throw new DirectoryUnableToRemoveException(previous: $e);
        }
    }
}
