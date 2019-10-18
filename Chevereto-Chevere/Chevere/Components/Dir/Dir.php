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

use InvalidArgumentException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;

use Chevere\Components\Message\Message;
use Chevere\Components\Path\Path;

final class Dir
{
    /** @var Path */
    private $path;

    public function __construct(string $path)
    {
        $path = new Path($path);
        if ($path->isFile()) {
            throw new InvalidArgumentException(
                (new Message('Path %path% is a file'))
                    ->code('%path%', $path->relative())
                    ->toString()
            );
        }
        $this->path = $path;
    }

    public function path(): Path
    {
        return $this->path;
    }

    /**
     * Creates the directory
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
     * Removes the directory
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
     * Removes the contents from a path without deleting the path.
     *
     * @return array List of deleted contents.
     */
    public function removeContents(): array
    {
        $this->assertIsDir();
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->path->absolute(), RecursiveDirectoryIterator::SKIP_DOTS),
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

    private function assertIsDir(): void
    {
        if (!$this->path->isDir()) {
            throw new InvalidArgumentException(
                (new Message('Path %path% is not a directory'))
                    ->code('%path%', $this->path->absolute())
                    ->toString()
            );
        }
    }
}
