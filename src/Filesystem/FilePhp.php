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

use Chevere\Filesystem\Exceptions\FileNotPhpException;
use Chevere\Filesystem\Interfaces\FileInterface;
use Chevere\Filesystem\Interfaces\FilePhpInterface;
use RangeException;
use RuntimeException;
use function Chevere\Message\message;

final class FilePhp implements FilePhpInterface
{
    public function __construct(
        private FileInterface $file
    ) {
        $this->assertFilePhp();
    }

    public function file(): FileInterface
    {
        return $this->file;
    }

    /**
     * @codeCoverageIgnore
     * @infection-ignore-all
     */
    public function compileCache(): void
    {
        $this->file->assertExists();
        $path = $this->file->path()->__toString();
        $stat = stat($path);
        $mtime = ! $stat ? time() : $stat['mtime'];
        $past = $mtime - 10;
        touch($path, $past);
        if (opcache_get_status() === false) {
            throw new RangeException(
                (string) message('%s% is not enabled', s: 'OPcache')
            );
        }
        opcache_compile_file($path);
    }

    /**
     * @codeCoverageIgnore
     * @infection-ignore-all
     */
    public function flushCache(): void
    {
        if (! opcache_is_script_cached($this->file->path()->__toString())) {
            return;
        }
        if (! opcache_invalidate($this->file->path()->__toString())) {
            throw new RuntimeException(
                (string) message('%s% is not enabled', s: 'OPcache')
            );
        }
    }

    private function assertFilePhp(): void
    {
        if (! $this->file->isPhp()) {
            throw new FileNotPhpException(
                (string) message(
                    'Instance of `%className%` must represents a PHP script in the path `%path%`',
                    className: $this->file::class,
                    path: $this->file->path()->__toString()
                )
            );
        }
    }
}
