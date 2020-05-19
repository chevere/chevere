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

use Chevere\Components\Filesystem\Exceptions\FileNotPhpException;
use Chevere\Interfaces\Filesystem\FileInterface;
use Chevere\Interfaces\Filesystem\FilePhpInterface;
use Chevere\Components\Instances\BootstrapInstance;
use Chevere\Components\Message\Message;
use RuntimeException;
use Throwable;

/**
 * A wrapper for FileInterface to interact with .php files.
 */
class FilePhp implements FilePhpInterface
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
        $path = $this->file->path()->absolute();
        $past = BootstrapInstance::get()->time() - 10;
        touch($path, $past);
        try {
            if (!opcache_compile_file($path)) {
                throw new RuntimeException(
                    (new Message('Zend OPcache is disabled'))
                        ->toString()
                );
            }
        } catch (Throwable $e) {
            throw new RuntimeException(
                (new Message('Unable to compile cache for file %path%'))
                    ->code('%path%', $path)
                    ->code('%thrown%', $e->getMessage())
                    ->toString()
            );
        }
    }

    /**
     * Flushes OPCache.
     *
     * @codeCoverageIgnore
     * @throws RuntimeException
     */
    public function flush(): void
    {
        if (!opcache_is_script_cached($this->file->path()->absolute())) {
            return;
        }
        if (!opcache_invalidate($this->file->path()->absolute())) {
            throw new RuntimeException(
                (new Message('Opcode cache is disabled'))
                    ->toString()
            );
        }
    }

    private function assertFilePhp(): void
    {
        if (!$this->file->isPhp()) {
            throw new FileNotPhpException(
                (new Message('Instance of %className% must represents a PHP script in the path %path%'))
                    ->code('%className%', get_class($this->file))
                    ->code('%path%', $this->file->path()->absolute())
            );
        }
    }
}
