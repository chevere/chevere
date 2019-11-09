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

use Chevere\Components\File\Exceptions\FileNotPhpException;
use Chevere\Components\Message\Message;
use Chevere\Contracts\File\FileContract;

/**
 * OPCache compiler
 */
final class FileCompile
{
    /** @var FileContract */
    private $file;

    /**
     * Applies OPCache to the PHP file
     *
     * @throws FileNotPhpException If attempt to compile a non-PHP file.
     * @throws FileNotFoundException If attempt to compile a file that doesn't exists.
     */
    public function __construct(FileContract $file)
    {
        $this->file = $file;
        $this->assertPhpScript();
        $this->assertExists();
        $this->assertCompile();
    }

    private function assertPhpScript(): void
    {
        if (!$this->file->isPhp()) {
            throw new FileNotPhpException(
                (new Message("Instance of %className% doesn't represent a PHP file in the path %path%"))
                    ->code('%className%', get_class($this->file))
                    ->code('%path%', $this->file->path()->absolute())
                    ->toString()
            );
        }
    }

    private function assertExists(): void
    {
        if (!$this->file->exists()) {
            throw new FileNotFoundException(
                (new Message("Instance of %className% doesn't represent a existent file in the path %path%"))
                    ->code('%className%', get_class($this->file))
                    ->code('%path%', $this->file->path()->absolute())
                    ->toString()
            );
        }
    }

    private function assertCompile(): void
    {
        if (!opcache_compile_file($this->file->path()->absolute())) {
            throw new RuntimeException(
                (new Message('Unable to compile cache for file %file% (Opcode cache is disabled)'))
                    ->code('%file%', $this->file->path()->absolute())
                    ->toString()
            );
        }
    }
}
