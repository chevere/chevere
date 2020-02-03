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

use Chevere\Components\Filesystem\Exceptions\File\FileNotPhpException;
use Chevere\Components\Message\Message;
use Chevere\Components\Filesystem\Interfaces\File\FileInterface;
use Chevere\Components\Filesystem\Interfaces\File\FilePhpInterface;

/**
 * A wrapper for FileInterface to implement PHP files.
 */
final class PhpFile implements FilePhpInterface
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
