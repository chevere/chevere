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

namespace Chevere\Components\File;

use RuntimeException;
use Chevere\Components\App\Instances\BootstrapInstance;
use Chevere\Components\Message\Message;
use Chevere\Components\File\Interfaces\FileCompileInterface;
use Chevere\Components\File\Interfaces\FilePhpInterface;

/**
 * OPCache compiler.
 */
final class FileCompile implements FileCompileInterface
{
    private FilePhpInterface $filePhp;

    /**
     * Applies OPCache to the PHP file.
     *
     * @throws FileNotPhpException   if $file is not a PHP file
     * @throws FileNotFoundException if $file doesn't exists
     */
    public function __construct(FilePhpInterface $filePhp)
    {
        $this->filePhp = $filePhp;
    }

    /**
     * {@inheritdoc}
     */
    public function filePhp(): FilePhpInterface
    {
        return $this->filePhp;
    }

    /**
     * {@inheritdoc}
     */
    public function compile(): void
    {
        $this->filePhp->file()->assertExists();
        $path = $this->filePhp->file()->path()->absolute();
        $past = BootstrapInstance::get()->time() - 10;
        touch($path, $past);
        /** @scrutinizer ignore-unhandled */ @opcache_invalidate($path, true);
        if (!opcache_compile_file($path)) {
            throw new RuntimeException(
                (new Message('Unable to compile cache for file %path% (Opcache is disabled)'))
                    ->code('%path%', $path)
                    ->toString()
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function destroy(): void
    {
        if (!opcache_invalidate($this->filePhp->file()->path()->absolute())) {
            throw new RuntimeException(
                (new Message('Opcode cache is disabled'))
                    ->toString()
            );
        }
    }
}
