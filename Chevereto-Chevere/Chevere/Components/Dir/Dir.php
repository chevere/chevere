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

namespace Chevere\Components\Dir;

use Chevere\Components\Path\Exceptions\PathIsFileException;
use Chevere\Components\Path\Exceptions\PathIsNotDirectoryException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use Chevere\Components\Message\Message;
use Chevere\Contracts\Dir\DirContract;
use Chevere\Contracts\Path\PathContract;

/**
 * This class provides interactions for a directory in the application namespace.
 */
final class Dir implements DirContract
{
    /** @var PathContract */
    private $path;

    /**
     * {@inheritdoc}
     */
    public function __construct(PathContract $path)
    {
        $this->path = $path;
        $this->assertIsNotFile();
    }

    /**
     * {@inheritdoc}
     */
    public function path(): PathContract
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
    public function create(): void
    {
        if ($this->path->exists()) {
            throw new RuntimeException(
                (new Message('Directory %directory% already exists'))
                    ->code('%directory%', $this->path->absolute())
                    ->toString()
            );
        }
        if (!mkdir($this->path->absolute(), 0777, true)) {
            throw new RuntimeException(
                (new Message('Unable to create directory %directory%'))
                    ->code('%directory%', $this->path->absolute())
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
        if (!rmdir($this->path->absolute())) {
            throw new RuntimeException(
                (new Message('Unable to remove directory %directory%'))
                    ->code('%directory%', $this->path->absolute())
                    ->toString()
            );
        }
        $array[] = $this->path->absolute();

        return $array;
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
            $content = $fileinfo->getRealPath();
            if ($fileinfo->isDir()) {
                if (!rmdir($content)) {
                    throw new RuntimeException(
                        (new Message('Unable to remove directory %directory%'))
                            ->code('%directory%', $this->path->absolute())
                            ->toString()
                    );
                }
                $removed[] = $content;
                continue;
            }
            if (!unlink($content)) {
                throw new RuntimeException(
                    (new Message('Unable to remove file %file%'))
                        ->code('%file%', $this->path->absolute())
                        ->toString()
                );
            }
            $removed[] = $content;
        }

        return $removed;
    }

    private function assertIsNotFile(): void
    {
        if ($this->path->isFile()) {
            throw new PathIsFileException(
                (new Message('Path %path% is a file'))
                    ->code('%path%', $this->path->relative())
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
