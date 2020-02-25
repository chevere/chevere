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

use Chevere\Components\Instances\BootstrapInstance;
use Chevere\Components\Filesystem\Exceptions\File\FileNotPhpException;
use Chevere\Components\Message\Message;
use Chevere\Components\Filesystem\Interfaces\File\FileInterface;
use Chevere\Components\Filesystem\Interfaces\File\FilePhpInterface;
use LogicException;
use RuntimeException;
use Throwable;

/**
 * A wrapper for FileInterface to implement PHP files.
 */
final class PhpFile implements FilePhpInterface
{
    private FileInterface $file;

    private bool $canCompile = false;

    private array $canCompileErrors = [];

    public function __construct(FileInterface $file)
    {
        $this->file = $file;
        $this->assertFilePhp();
        $this->handleCanCompile();
    }

    public function file(): FileInterface
    {
        return $this->file;
    }

    public function canCompile(): bool
    {
        return $this->canCompile;
    }

    /**
     * @codeCoverageIgnore
     * @throws RuntimeException
     * @throws LogicException
     */
    public function compile(): void
    {
        $this->assertCompile();
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
     * @codeCoverageIgnore
     * @throws RuntimeException
     */
    public function destroy(): void
    {
        $this->assertCompile();
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
                    ->toString()
            );
        }
    }

    /**
     * @codeCoverageIgnore
     */
    private function handleCanCompile(): void
    {
        if (!extension_loaded('Zend OPcache')) {
            $this->canCompileErrors[] = (new Message('Extension %extension% is not loaded'))
                ->code('%extension%', 'Zend OPcache')
                ->toString();
        }
        foreach (['opcache.enable', 'opcache.enable_cli'] as $setting) {
            if (ini_get($setting) !== '1') {
                $this->canCompileErrors[] = (new Message('Missing ini setting %ini%'))
                    ->code('%ini%', $setting . '=1')
                    ->toString();
            }
        }
        if ($this->canCompileErrors === []) {
            $this->canCompile = true;

            return;
        }
    }

    /**
     * @codeCoverageIgnore
     * @throws RuntimeException
     */
    private function assertCompile(): void
    {
        if ($this->canCompile === true) {
            return;
        }
        throw new RuntimeException(
            (new Message('Unable to compile: %errors%'))
                ->code('%errors%', implode('; ', $this->canCompileErrors))
                ->toString()
        );
    }
}
