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
use RuntimeException;

use Chevere\Components\Message\Message;
use Chevere\Components\Path\Path;

/**
 * This class provides interactions for a file in the application namespace.
 */
final class File
{
    /** @var Path */
    private $path;

    public function __construct(Path $path)
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

    public function path(): Path
    {
        return $this->path;
    }

    public function exists(): bool
    {
        return $this->path->exists() && $this->path->isFile();
    }

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

    public function put(string $contents): void
    {
        if (!$this->path->exists()) {
            $dirname = dirname($this->path->absolute());
            $path = new Path($dirname);
            if (!$path->exists()) {
                (new Dir($path))->create();
            }
        }
        if (false === @file_put_contents($this->path->absolute(), $contents)) {
            throw new RuntimeException(
                (new Message('Unable to write content to file %filepath%'))
                    ->code('%filepath%', $this->path->absolute())
                    ->toString()
            );
        }
    }
}
