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

use BadMethodCallException;
use InvalidArgumentException;
use RuntimeException;

use Chevere\Components\Dir\Dir;
use Chevere\Components\Message\Message;
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

    /**
     * {@inheritdoc}
     */
    public function __construct(PathContract $path)
    {
        if ($path->isDir()) {
            throw new InvalidArgumentException(
                (new Message('Path %path% is a directory'))
                    ->code('%path%', $path->relative())
                    ->toString()
            );
        }
        $this->path = $path;
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
        return $this->path->exists() && $this->path->isFile();
    }

    /**
     * {@inheritdoc}
     */
    public function remove(): void
    {
        if (!unlink($this->path->absolute())) {
            throw new RuntimeException(
                (new Message('Unable to remove file %path%'))
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
            $dirname = dirname($this->path->absolute());
            $path = new Path($dirname);
            if (!$path->exists()) {
                (new Dir($path))->create();
            }
        }
        if (false === file_put_contents($this->path->absolute(), $contents)) {
            throw new RuntimeException(
                (new Message('Unable to write content to file %filepath%'))
                    ->code('%filepath%', $this->path->absolute())
                    ->toString()
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function compile(): void
    {
        $this->assertPhpScript();
        if (!opcache_compile_file($this->path->absolute())) {
            throw new RuntimeException(
                (new Message('Unable to compile cache for file %file% (Opcode cache is disabled)'))
                    ->code('%file%', $this->path->absolute())
                    ->toString()
            );
        }
    }

    private function assertPhpScript(): void
    {
        if (!stringEndsWith('.php', $this->path->absolute())) {
            throw new BadMethodCallException(
                (new Message("The file at %path% is not a PHP script"))
                    ->code('%path%', $this->path->absolute())
                    ->toString()
            );
        }
    }
}
