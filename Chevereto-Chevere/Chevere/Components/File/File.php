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

namespace Chevere\Components\File;

use Chevere\Components\Dir\Dir;
use Chevere\Components\File\Exceptions\FileAlreadyExistsException;
use Chevere\Components\File\Exceptions\FileNotFoundException;
use Chevere\Components\File\Exceptions\FileUnableToCreateException;
use Chevere\Components\File\Exceptions\FileUnableToPutException;
use Chevere\Components\File\Exceptions\FileUnableToRemoveException;
use Chevere\Components\Message\Message;
use Chevere\Components\Path\Exceptions\PathIsDirException;
use Chevere\Components\Path\Path;
use Chevere\Contracts\File\FileContract;
use Chevere\Contracts\Path\PathContract;
use function ChevereFn\stringEndsWith;

/**
 * This class provides interactions for a file in the application namespace.
 */
final class File implements FileContract
{
    /** @var PathContract */
    private $path;

    /** @var bool */
    private $isPhp;

    /**
     * {@inheritdoc}
     */
    public function __construct(PathContract $path)
    {
        $this->path = $path;
        $this->isPhp = stringEndsWith('.php', $this->path->absolute());
        $this->assertIsNotDir();
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
    public function isPhp(): bool
    {
        return $this->isPhp;
    }

    /**
     * {@inheritdoc}
     */
    public function exists(): bool
    {
        return $this->path->exists() && $this->path->isFile();
    }

    /**
     * {@inheritdoc}
     */
    public function remove(): void
    {
        if (!$this->exists()) {
            throw new FileNotFoundException(
                (new Message("The file %path% doesn't exists"))
                    ->code('%path%', $this->path->absolute())
                    ->toString()
            );
        }
        if (!@unlink($this->path->absolute())) {
            throw new FileUnableToRemoveException(
                (new Message('Unable to remove file %path%'))
                    ->code('%path%', $this->path->absolute())
                    ->toString()
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function create(): void
    {
        if ($this->path->exists()) {
            throw new FileAlreadyExistsException(
                (new Message('Unable to create file %path% (file already exists)'))
                    ->code('%path%', $this->path->absolute())
                    ->toString()
            );
        }
        $this->createPath();
        if (false === file_put_contents($this->path->absolute(), null)) {
            throw new FileUnableToCreateException(
                (new Message('Unable to create file %path% (file system error)'))
                    ->code('%path%', $this->path->absolute())
                    ->toString()
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function put(string $contents): void
    {
        if (!$this->path->exists()) {
            $this->createPath();
        }
        if (false === file_put_contents($this->path->absolute(), $contents)) {
            throw new FileUnableToPutException(
                (new Message('Unable to write content to file %filepath%'))
                    ->code('%filepath%', $this->path->absolute())
                    ->toString()
            );
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
                    ->code('%path%', $this->path->relative())
                    ->toString()
            );
        }
    }
}
