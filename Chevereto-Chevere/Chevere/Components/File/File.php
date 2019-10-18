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

use Chevere\Components\Dir\Dir;
use RuntimeException;

use Chevere\Components\Message\Message;
use Chevere\Components\Path\Path;

final class File
{
    /** @var Path */
    private $path;

    public function __construct(Path $path)
    {
        $this->path = $path;
    }

    public function remove(): void
    {
        if (!unlink($this->path->absolute())) {
            throw new RuntimeException(
                (new Message('Unable to remove file %path%'))
                    ->code('%path%', $this->path->absolute())
                    ->toString()
            );
        }
    }

    public function put(string $contents): void
    {
        if (!$this->path->exists()) {
            $dirname = dirname($this->path->absolute());
            $dir = new Dir($dirname);
            if (!$dir->path()->exists()) {
                $dir->create();
            }
        }
        if (false === @file_put_contents($this->path->absolute(), $contents)) {
            throw new RuntimeException(
                (new Message('Unable to write content to file %filepath%'))
                    ->code('%filepath%', $this->path->absolute())
                    ->toString()
            );
        }
    }
}
