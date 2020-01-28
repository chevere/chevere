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

namespace Chevere\Components\Dir;

use Chevere\Components\Dir\Exceptions\DirUnableToCreateException;
use Chevere\Components\Dir\Exceptions\DirUnableToRemoveException;
use Chevere\Components\File\Exceptions\FileUnableToRemoveException;
use Chevere\Components\Message\Message;
use Chevere\Components\Path\Exceptions\PathIsFileException;
use Chevere\Components\Path\Exceptions\PathIsNotDirectoryException;
use Chevere\Components\Dir\Interfaces\DirInterface;
use Chevere\Components\File\File;
use Chevere\Components\Path\Interfaces\PathInterface;
use Chevere\Components\Path\Path;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Throwable;

/**
 * This class provides interactions for a directory in the application namespace.
 */
final class Dir implements DirInterface
{
    private PathInterface $path;

    /**
     * Creates a new instance.
     *
     * @throws PathIsFileException if the PathInterface represents a file
     */
    public function __construct(PathInterface $path)
    {
        $this->path = $path;
        $this->assertIsNotFile();
    }

    /**
     * {@inheritdoc}
     */
    public function path(): PathInterface
    {
        return $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function exists(): bool
    {
        return $this->path->exists() && $this->path->isDir();
    }

    /**
     * {@inheritdoc}
     */
    public function create(int $mode = 0777): void
    {
        try {
            !mkdir($this->path->absolute(), $mode, true);
        } catch (Throwable $e) {
            throw new DirUnableToCreateException(
                (new Message('Unable to create directory %path% %thrown%'))
                    ->code('%path%', $this->path->absolute())
                    ->code('%thrown%', '[' . $e->getMessage() . ']')
                    ->toString()
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function remove(): array
    {
        $this->assertIsDir();
        $array = $this->removeContents();
        $this->rmdir();
        $array[] = $this->path->absolute();

        return $array;
    }

    /**
     * {@inheritdoc}
     */
    public function rmdir(): void
    {
        try {
            rmdir($this->path->absolute());
        } catch (Throwable $e) {
            throw new DirUnableToRemoveException(
                (new Message('Unable to remove directory %path% %thrown%'))
                    ->code('%path%', $this->path->absolute())
                    ->code('%thrown%', '[' . $e->getMessage() . ']')
                    ->toString()
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeContents(): array
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
        foreach ($files as $fileinfo) {
            $path = new Path($fileinfo->getRealPath());
            if ($fileinfo->isDir()) {
                (new Dir($path))->rmdir();
                $removed[] = $path->absolute();
                continue;
            }
            (new File($path))->remove();
            $removed[] = $path->absolute();
        }

        return $removed;
    }

    /**
     * {@inheritdoc}
     */
    public function getChild(string $path): DirInterface
    {
        return new Dir(
            $this->path->getChild($path)
        );
    }

    private function assertIsNotFile(): void
    {
        if ($this->path->isFile()) {
            throw new PathIsFileException(
                (new Message('Path %path% is a file'))
                    ->code('%path%', $this->path->absolute())
                    ->toString()
            );
        }
    }

    private function assertIsDir(): void
    {
        if (!$this->path->isDir()) {
            throw new PathIsNotDirectoryException(
                (new Message('Path %path% is not a directory'))
                    ->code('%path%', $this->path->absolute())
                    ->toString()
            );
        }
    }
}
