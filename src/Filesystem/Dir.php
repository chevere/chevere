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

use Chevere\Filesystem\Exceptions\DirExistsException;
use Chevere\Filesystem\Exceptions\DirNotExistsException;
use Chevere\Filesystem\Exceptions\DirUnableToCreateException;
use Chevere\Filesystem\Exceptions\DirUnableToRemoveException;
use Chevere\Filesystem\Exceptions\PathIsFileException;
use Chevere\Filesystem\Exceptions\PathIsNotDirectoryException;
use Chevere\Filesystem\Exceptions\PathTailException;
use Chevere\Filesystem\Interfaces\DirInterface;
use Chevere\Filesystem\Interfaces\PathInterface;
use function Chevere\Iterator\recursiveDirectoryIteratorFor;
use Chevere\Message\Message;
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
                    ->code('%path%', $this->path->__toString())
            );
        }
    }

    // @infection-ignore-all
    public function create(int $mode = 0755): void
    {
        if ($this->exists()) {
            throw new DirExistsException(
                (new Message('Directory %path% already exists'))
                    ->code('%path%', $this->path->__toString())
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
        if (!$this->exists()) {
            return [];
        }

        return $this->remove();
    }

    public function removeContents(): array
    {
        $this->assertIsDir();
        $files = new RecursiveIteratorIterator(
            recursiveDirectoryIteratorFor($this, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        $removed = [];
        /** @var SplFileInfo */
        foreach ($files as $fileInfo) {
            if ($fileInfo->isDir()) {
                $realPath = tailDirPath($fileInfo->getRealPath());
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
                (new Message('Path %path% is a file'))
                    ->code('%path%', $this->path->__toString())
            );
        }
    }

    private function assertTailDir(): void
    {
        $absolute = $this->path->__toString();
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
                    ->code('%path%', $this->path->__toString())
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
            throw new DirUnableToCreateException(previous: $e, );
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
            throw new DirUnableToRemoveException(previous: $e);
        }
    }
}
