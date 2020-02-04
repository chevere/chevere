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

use RuntimeException;
use Chevere\Components\App\Instances\BootstrapInstance;
use Chevere\Components\Message\Message;
use Chevere\Components\Filesystem\Interfaces\File\FileCompileInterface;
use Chevere\Components\Filesystem\Interfaces\File\FilePhpInterface;
use Throwable;

/**
 * OPCache compiler.
 */
final class FileCompile implements FileCompileInterface
{
    private FilePhpInterface $filePhp;

    /**
     * Applies OPCache to the PHP file.
     *
     * @throws RuntimeException If Zend OPcache is not available
     * @throws FileNotPhpException   if $file is not a PHP file
     * @throws FileNotFoundException if $file doesn't exists
     */
    public function __construct(FilePhpInterface $filePhp)
    {
        if (!extension_loaded('Zend OPcache')) {
            throw new RuntimeException(
                (new Message('Extension %extension% is not loaded'))
                    ->code('%extension%', 'Zend OPcache')
                    ->toString()
            );
        }

        if (ini_get('opcache.enable') === '0' || ini_get('opcache.enable_cli') === '0') {
            throw new RuntimeException(
                (new Message('Extension %extension% must be enabled with %enable% and %enableCli% in a parsed configuration file (%iniFile%)'))
                    ->code('%extension%', 'Zend OPcache')
                    ->code('%enable%', 'opcache.enable = 1')
                    ->code('%enableCli%', 'opcache.enable_cli = 1')
                    ->code('%iniFile%', '.ini')
                    ->toString()
            );
        }
        $this->filePhp = $filePhp;
    }

    public function filePhp(): FilePhpInterface
    {
        return $this->filePhp;
    }

    public function compile(): void
    {
        $this->filePhp->file()->assertExists();
        $path = $this->filePhp->file()->path()->absolute();
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

    public function destroy(): void
    {
        if (!opcache_is_script_cached($this->filePhp->file()->path()->absolute())) {

            return;
        }
        if (!opcache_invalidate($this->filePhp->file()->path()->absolute())) {
            throw new RuntimeException(
                (new Message('Opcode cache is disabled'))
                    ->toString()
            );
        }
    }
}
