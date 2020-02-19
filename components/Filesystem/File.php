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

use Chevere\Components\Filesystem\Dir;
use Chevere\Components\Filesystem\Exceptions\File\FileExistsException;
use Chevere\Components\Filesystem\Exceptions\File\FileNotFoundException;
use Chevere\Components\Filesystem\Exceptions\File\FileUnableToCreateException;
use Chevere\Components\Filesystem\Exceptions\File\FileUnableToGetException;
use Chevere\Components\Filesystem\Exceptions\File\FileUnableToPutException;
use Chevere\Components\Filesystem\Exceptions\File\FileUnableToRemoveException;
use Chevere\Components\Message\Message;
use Chevere\Components\Filesystem\Exceptions\Path\PathIsDirException;
use Chevere\Components\Filesystem\Interfaces\File\FileInterface;
use Chevere\Components\Filesystem\Interfaces\Path\PathInterface;
use Chevere\Components\Filesystem\Path;
use Chevere\Components\Str\StrBool;
use Exception;
use Throwable;

/**
 * This class provides interactions for a file in the application namespace.
 */
final class File implements FileInterface
{
    private PathInterface $path;

    private bool $isPhp;

    /**
     * Creates a new instance.
     *
     * @throws PathIsDirException if the PathInterface represents a directory
     */
    public function __construct(PathInterface $path)
    {
        $this->path = $path;
        $this->isPhp = (new StrBool($this->path->absolute()))->endsWith('.php');
        $this->assertIsNotDir();
    }

    public function path(): PathInterface
    {
        return $this->path;
    }

    public function isPhp(): bool
    {
        return $this->isPhp;
    }

    public function exists(): bool
    {
        return $this->path->exists() && $this->path->isFile();
    }

    public function assertExists(): void
    {
        if (!$this->exists()) {
            throw new FileNotFoundException(
                (new Message("File %path% doesn't exists"))
                    ->code('%path%', $this->path->absolute())
                    ->toString()
            );
        }
    }

    public function checksum(): string
    {
        $this->assertExists();

        return hash_file(FileInterface::CHECKSUM_ALGO, $this->path->absolute());
    }

    /**
     * @codeCoverageIgnoreStart
     * @throws FileNotFoundException
     * @throws FileUnableToGetException
     */
    public function contents(): string
    {
        $this->assertExists();
        try {
            $contents = file_get_contents($this->path->absolute());
            if (false === $contents) {
                throw new Exception(
                    (new Message('Failure in function %functionName%'))
                        ->code('%functionName%', 'file_get_contents')
                        ->toString()
                );
            }
        } catch (Throwable $e) {
            throw new FileUnableToGetException(
                (new Message('Unable to read the contents of the file at %path%'))
                    ->code('%path%', $this->path->absolute())
                    ->toString()
            );
        }

        return $contents;
    }

    public function remove(): void
    {
        $this->assertExists();
        // @codeCoverageIgnoreStart
        try {
            unlink($this->path->absolute());
        } catch (Throwable $e) {
            throw new FileUnableToRemoveException(
                (new Message('Unable to remove file %path%'))
                    ->code('%path%', $this->path->absolute())
                    ->toString()
            );
        }
        // @codeCoverageIgnoreEnd
    }

    public function create(): void
    {
        $this->assertIsNotDir();
        if ($this->path->exists()) {
            throw new FileExistsException(
                (new Message('Unable to create file %path% (file already exists)'))
                    ->code('%path%', $this->path->absolute())
                    ->toString()
            );
        }
        $this->createPath();
        if (false === file_put_contents($this->path->absolute(), null)) {
            // @codeCoverageIgnoreStart
            throw new FileUnableToCreateException(
                (new Message('Unable to create file %path% (file system error)'))
                    ->code('%path%', $this->path->absolute())
                    ->toString()
            );
            // @codeCoverageIgnoreEnd
        }
    }

    public function put(string $contents): void
    {
        $this->assertExists();
        if (false === file_put_contents($this->path->absolute(), $contents)) {
            // @codeCoverageIgnoreStart
            throw new FileUnableToPutException(
                (new Message('Unable to write content to file %filepath%'))
                    ->code('%filepath%', $this->path->absolute())
                    ->toString()
            );
            // @codeCoverageIgnoreEnd
        }
    }

    private function createPath(): void
    {
        $dirname = dirname($this->path->absolute());
        $path = new Path($dirname);
        if (!$path->exists()) {
            (new Dir($path))->create();
        }
    }

    private function assertIsNotDir(): void
    {
        if ($this->path->isDir()) {
            throw new PathIsDirException(
                (new Message('Path %path% is a directory'))
                    ->code('%path%', $this->path->absolute())
                    ->toString()
            );
        }
    }
}
