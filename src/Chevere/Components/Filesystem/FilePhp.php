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
use Chevere\Exceptions\Core\RangeException;
use Chevere\Exceptions\Core\RuntimeException;
use Chevere\Exceptions\Filesystem\FileNotPhpException;
use Chevere\Interfaces\Filesystem\FileInterface;
use Chevere\Interfaces\Filesystem\FilePhpInterface;
use Throwable;

final class FilePhp implements FilePhpInterface
{
    private FileInterface $file;

    public function __construct(FileInterface $file)
    {
        $this->file = $file;
        $this->assertFilePhp();
    }

    public function file(): FileInterface
    {
        return $this->file;
    }

    /**
     * @codeCoverageIgnore
     */
    public function cache(): void
    {
        $this->file->assertExists();
        $path = $this->file->path()->toString();
        $past = stat($path)['mtime'] - 10;
        touch($path, $past);

        try {
            if (! opcache_compile_file($path)) {
                throw new RangeException(
                    (new Message('OPcache is disabled'))
                );
            }
        } catch (Throwable $e) {
            throw new RuntimeException(
                (new Message('Unable to compile cache for file %path% %thrown%'))
                    ->code('%path%', $path)
                    ->code('%thrown%', $e->getMessage())
            );
        }
    }

    /**
     * @codeCoverageIgnore
     */
    public function flush(): void
    {
        if (! opcache_is_script_cached($this->file->path()->toString())) {
            return;
        }
        if (! opcache_invalidate($this->file->path()->toString())) {
            throw new RuntimeException(
                (new Message('OPCache is disabled'))
            );
        }
    }

    private function assertFilePhp(): void
    {
        if (! $this->file->isPhp()) {
            throw new FileNotPhpException(
                (new Message('Instance of %className% must represents a PHP script in the path %path%'))
                    ->code('%className%', get_class($this->file))
                    ->code('%path%', $this->file->path()->toString())
            );
        }
    }
}
