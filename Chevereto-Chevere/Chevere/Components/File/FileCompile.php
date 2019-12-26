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
use Chevere\Contracts\File\FileCompileContract;
use Chevere\Contracts\File\FilePhpContract;
use const Chevere\BOOTSTRAP_TIME;

/**
 * OPCache compiler.
 */
final class FileCompile implements FileCompileContract
{
    private FilePhpContract $filePhp;

    /**
     * {@inheritdoc}
     */
    public function __construct(FilePhpContract $filePhp)
    {
        $this->filePhp = $filePhp;
    }

    /**
     * {@inheritdoc}
     */
    public function filePhp(): FilePhpContract
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
        $past = BOOTSTRAP_TIME - 10;
        touch($path, $past);
        @opcache_invalidate($path, true);
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
