<?php

declare(strict_types=1);

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevere\File;

use RuntimeException;
use Chevere\Message\Message;
use Chevere\Path\Path;

use function ChevereFn\pathIsAbsolute;
use function ChevereFn\pathAbsolute;
use function ChevereFn\pathCreate;

final class File
{
    /** @var string Absolute filename */
    private $path;

    public function __construct(string $path)
    {
        $this->path = $path;
        if (!pathIsAbsolute($path)) {
            $this->path = pathAbsolute($path);
        }
    }

    /**
     * Fast wat to determine if a file or directory exists using stream_resolve_include_path.
     *
     * @return bool TRUE if the $filename exists
     */
    // FIXME: This should be moved to Path
    public function exists(): bool
    {
        $this->clearStatCache();

        return stream_resolve_include_path($this->path) !== false;
    }

    public function put(string $contents): void
    {
        if (!$this->exists()) {
            $dirname = dirname($this->path);
            $new = new static($dirname);
            if (!$new->exists()) {
                pathCreate($dirname);
            }
        }
        if (false === @file_put_contents($this->path, $contents)) {
            throw new RuntimeException(
                (new Message('Unable to write content to file %filepath%'))
                    ->code('%filepath%', $this->path)
                    ->toString()
            );
        }
    }

    private function clearStatCache(): void
    {
        clearstatcache(true, $this->path);
    }
}
