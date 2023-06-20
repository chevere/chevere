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

use Chevere\Filesystem\Exceptions\FileNotExistsException;
use Chevere\Filesystem\Exceptions\FileUnableToCreateException;
use Chevere\Filesystem\Exceptions\FileUnableToGetException;
use Chevere\Filesystem\Exceptions\FileUnableToPutException;
use Chevere\Filesystem\Exceptions\FileUnableToRemoveException;
use Chevere\Filesystem\Exceptions\PathExistsException;
use Chevere\Filesystem\Exceptions\PathIsDirectoryException;
use Chevere\Filesystem\Interfaces\FileInterface;
use Chevere\Filesystem\Interfaces\PathInterface;
use Chevere\Throwable\Exceptions\RuntimeException;
use Throwable;
use function Chevere\Message\message;
use function Safe\file_get_contents;
use function Safe\file_put_contents;
use function Safe\filesize;
use function Safe\unlink;

final class File implements FileInterface
{
    private bool $isPhp;

    public function __construct(
        private PathInterface $path
    ) {
        $this->isPhp = str_ends_with($this->path->__toString(), '.php');
        $this->assertIsNotDirectory();
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
        return $this->path->isFile();
    }

    public function assertExists(): void
    {
        if (! $this->exists()) {
            throw new FileNotExistsException(
                message("File %path% doesn't exists")
                    ->withCode('%path%', $this->path->__toString())
            );
        }
    }

    public function getChecksum(): string
    {
        $this->assertExists();
        $hashFile = hash_file(FileInterface::CHECKSUM_ALGO, $this->path->__toString());
        if (is_string($hashFile)) {
            return $hashFile;
        }
        // @codeCoverageIgnoreStart
        // @infection-ignore-all
        throw new RuntimeException(
            message: message('Unable to get checksum for file %path%')
                ->withCode('%path%', $this->path->__toString())
        );
        // @codeCoverageIgnoreEnd
    }

    public function getSize(): int
    {
        $this->assertExists();

        return filesize($this->path->__toString());
    }

    /**
     * @throws FileNotExistsException
     * @throws FileUnableToGetException
     */
    public function getContents(): string
    {
        $this->assertExists();

        try {
            return file_get_contents($this->path->__toString());
        }
        // @codeCoverageIgnoreStart
        // @infection-ignore-all
        catch (Throwable $e) {
            throw new FileUnableToGetException(
                message('Unable to read the contents of the file at %path%')
                    ->withCode('%path%', $this->path->__toString())
            );
        }
        // @codeCoverageIgnoreEnd
    }

    public function remove(): void
    {
        $this->assertExists();
        $this->assertUnlink();
    }

    public function removeIfExists(): void
    {
        if (! $this->exists()) {
            return;
        }
        $this->assertUnlink();
    }

    public function create(): void
    {
        if ($this->path->exists()) {
            throw new PathExistsException(
                message('Unable to create file %path% (file already exists)')
                    ->withCode('%path%', $this->path->__toString())
            );
        }
        $this->createPath();

        try {
            file_put_contents($this->path->__toString(), '');
        }
        // @codeCoverageIgnoreStart
        // @infection-ignore-all
        catch (Throwable $e) {
            throw new FileUnableToCreateException(previous: $e);
        }
        // @codeCoverageIgnoreEnd
    }

    public function createIfNotExists(): void
    {
        if ($this->exists()) {
            return;
        }
        $this->create();
    }

    public function put(string $contents): void
    {
        $this->assertExists();

        try {
            file_put_contents($this->path->__toString(), $contents);
        }
        // @codeCoverageIgnoreStart
        // @infection-ignore-all
        catch (Throwable $e) {
            throw new FileUnableToPutException(
                message('Unable to write content to file %filepath%')
                    ->withCode('%filepath%', $this->path->__toString())
            );
        }
        // @codeCoverageIgnoreEnd
    }

    private function createPath(): void
    {
        $dirname = dirname($this->path->__toString());
        $path = new Path($dirname . '/');
        if (! $path->exists()) {
            (new Directory($path))->create();
        }
    }

    private function assertIsNotDirectory(): void
    {
        if ($this->path->isDirectory()) {
            throw new PathIsDirectoryException(
                message('Path %path% is a directory')
                    ->withCode('%path%', $this->path->__toString())
            );
        }
    }

    private function assertUnlink(): void
    {
        try {
            unlink($this->path->__toString());
        }
        // @codeCoverageIgnoreStart
        // @infection-ignore-all
        catch (Throwable $e) {
            throw new FileUnableToRemoveException(previous: $e);
        }
        // @codeCoverageIgnoreEnd
    }
}
