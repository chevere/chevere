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
use Chevere\Contracts\File\FileContract;
use Chevere\Contracts\File\FilePhpContract;

/**
 * OPCache compiler
 */
final class FileCompile implements FileCompileContract
{
    /** @var FilePhpContract */
    private $filePhp;

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
    public function file(): FileContract
    {
        return $this->filePhp->file();
    }

    /**
     * {@inheritdoc}
     */
    public function compile(): void
    {
        if (!opcache_compile_file($this->file()->path()->absolute())) {
            throw new RuntimeException(
                (new Message('Unable to compile cache for file %file% (Opcache is disabled)'))
                    ->code('%file%', $this->file()->path()->absolute())
                    ->toString()
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function destroy(): void
    {
        if (!opcache_invalidate($this->file()->path()->absolute())) {
            throw new RuntimeException(
                (new Message('Opcode cache is disabled'))
                    ->toString()
            );
        }
    }
}
