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

use RuntimeException;

use Chevere\Components\Message\Message;
use Chevere\Components\Path\Path;

final class File
{
    /** @var Path */
    private $path;

    public function __construct(Path $path)
    {
        $this->path = $path;
    }

    /**
     * Fast wat to determine if a file or directory exists using stream_resolve_include_path.
     *
     * @return bool TRUE if the file exists
     */
    public function exists(): bool
    {
        $this->clearStatCache();

        return stream_resolve_include_path($this->path->absolute()) !== false;
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
        if (!$this->exists()) {
            $dirname = dirname($this->path->absolute());
            $path = new Path($dirname);
            if (!$path->isDir()) {
                $path->create();
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

    private function clearStatCache(): void
    {
        clearstatcache(true, $this->path->absolute());
    }
}
