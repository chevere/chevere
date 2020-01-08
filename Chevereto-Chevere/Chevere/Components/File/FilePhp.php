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

use Chevere\Components\File\Exceptions\FileNotPhpException;
use Chevere\Components\Message\Message;
use Chevere\Components\File\Contracts\FileContract;
use Chevere\Components\File\Contracts\FilePhpContract;

/**
 * A wrapper for FileContract to implement PHP files.
 */
final class FilePhp implements FilePhpContract
{
    private FileContract $file;

    /**
     * {@inheritdoc}
     */
    public function __construct(FileContract $file)
    {
        $this->file = $file;
        $this->assertFilePhp();
    }

    /**
     * {@inheritdoc}
     */
    public function file(): FileContract
    {
        return $this->file;
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
}
