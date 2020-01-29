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

use Chevere\Components\File\Exceptions\FileNotPhpException;
use Chevere\Components\Message\Message;
use Chevere\Components\File\Interfaces\FileInterface;
use Chevere\Components\File\Interfaces\FilePhpInterface;

/**
 * A wrapper for FileInterface to implement PHP files.
 */
final class FilePhp implements FilePhpInterface
{
    private FileInterface $file;

    /**
     * Creates a new instance.
     *
     * @throws FileNotPhpException If $file is not a PHP file.
     */
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
