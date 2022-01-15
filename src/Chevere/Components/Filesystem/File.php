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
use Chevere\Components\Str\StrBool;
use Chevere\Exceptions\Filesystem\FileNotExistsException;
use Chevere\Exceptions\Filesystem\FileUnableToCreateException;
use Chevere\Exceptions\Filesystem\FileUnableToGetException;
use Chevere\Exceptions\Filesystem\FileUnableToPutException;
use Chevere\Exceptions\Filesystem\FileUnableToRemoveException;
use Chevere\Exceptions\Filesystem\PathExistsException;
use Chevere\Exceptions\Filesystem\PathIsDirException;
use Chevere\Interfaces\Filesystem\FileInterface;
use Chevere\Interfaces\Filesystem\PathInterface;
use function Safe\file_get_contents;
use function Safe\file_put_contents;
use function Safe\filesize;
use function Safe\unlink;
use Throwable;

final class File implements FileInterface
{
    private bool $isPhp;

    public function __construct(
        private PathInterface $path
    ) {
        $this->isPhp = (new StrBool($this->path->toString()))->endsWith('.php');
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
        return $this->path->isFile();
    }

    public function assertExists(): void
    {
        if (!$this->exists()) {
            throw new FileNotExistsException(
                (new Message("File %path% doesn't exists"))
                    ->code('%path%', $this->path->toString())
            );
        }
    }

    public function getChecksum(): string
    {
        $this->assertExists();

        return hash_file(FileInterface::CHECKSUM_ALGO, $this->path->toString());
    }

    public function getSize(): int
    {
        $this->assertExists();

        return filesize($this->path->toString());
    }

    /**
     * @throws FileNotExistsException
     * @throws FileUnableToGetException
     */
    public function getContents(): string
    {
        $this->assertExists();

        try {
            return file_get_contents($this->path->toString());
        }
        // @codeCoverageIgnoreStart
        // @infection-ignore-all
        catch (Throwable $e) {
            throw new FileUnableToGetException(
                (new Message('Unable to read the contents of the file at %path%'))
                    ->code('%path%', $this->path->toString())
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
        if (!$this->exists()) {
            return;
        }
        $this->assertUnlink();
    }

    public function create(): void
    {
        if ($this->path->exists()) {
            throw new PathExistsException(
                (new Message('Unable to create file %path% (file already exists)'))
                    ->code('%path%', $this->path->toString())
            );
        }
        $this->createPath();

        try {
            file_put_contents($this->path->toString(), '');
        }
        // @codeCoverageIgnoreStart
        // @infection-ignore-all
        catch (Throwable $e) {
            throw new FileUnableToCreateException(previous: $e);
        }
        // @codeCoverageIgnoreEnd
    }

    public function put(string $contents): void
    {
        $this->assertExists();

        try {
            file_put_contents($this->path->toString(), $contents);
        }
        // @codeCoverageIgnoreStart
        // @infection-ignore-all
        catch (Throwable $e) {
            throw new FileUnableToPutException(
                (new Message('Unable to write content to file %filepath%'))
                    ->code('%filepath%', $this->path->toString())
            );
        }
        // @codeCoverageIgnoreEnd
    }

    private function createPath(): void
    {
        $dirname = dirname($this->path->toString());
        $path = new Path($dirname . '/');
        if (!$path->exists()) {
            (new Dir($path))->create();
        }
    }

    private function assertIsNotDir(): void
    {
        if ($this->path->isDir()) {
            throw new PathIsDirException(
                (new Message('Path %path% is a directory'))
                    ->code('%path%', $this->path->toString())
            );
        }
    }

    private function assertUnlink(): void
    {
        try {
            unlink($this->path->toString());
        }
        // @codeCoverageIgnoreStart
        // @infection-ignore-all
        catch (Throwable $e) {
            throw new FileUnableToRemoveException(previous: $e);
        }
        // @codeCoverageIgnoreEnd
    }
}
