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

use Chevere\Components\Message\Message;
use Chevere\Components\Filesystem\Exceptions\Path\PathDotSlashException;
use Chevere\Components\Filesystem\Exceptions\Path\PathDoubleDotsDashException;
use Chevere\Components\Filesystem\Exceptions\Path\PathExtraSlashesException;
use Chevere\Components\Filesystem\Exceptions\Path\PathNotAbsoluteException;
use Chevere\Components\Filesystem\Interfaces\Path\PathFormatInterface;
use Chevere\Components\Str\StrBool;

final class AssertPath implements PathFormatInterface
{
    private string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
        $this->assertAbsolutePath();
        $this->assertNoDoubleDots();
        $this->assertNoDots();
        $this->assertNoExtraSlashes();
    }

    private function assertAbsolutePath(): void
    {
        if ((new StrBool($this->path))->startsWith('/') === false) {
            throw new PathNotAbsoluteException(
                (new Message('Path %path% must start with %char%'))
                    ->code('%path%', $this->path)
                    ->code('%char%', '/')
                    ->toString()
            );
        }
    }

    private function assertNoDoubleDots(): void
    {
        if (false !== strpos($this->path, '../')) {
            throw new PathDoubleDotsDashException(
                (new Message('Must omit %chars% for path %path%'))
                    ->code('%chars%', '../')
                    ->code('%path%', $this->path)
                    ->toString()
            );
        }
    }

    private function assertNoDots(): void
    {
        if (false !== strpos($this->path, './')) {
            throw new PathDotSlashException(
                (new Message('Must omit %chars% for path %path%'))
                    ->code('%chars%', './')
                    ->code('%path%', $this->path)
                    ->toString()
            );
        }
    }

    private function assertNoExtraSlashes(): void
    {
        if (false !== strpos($this->path, '//')) {
            throw new PathExtraSlashesException(
                (new Message('Path %path% contains extra-slashes'))
                    ->code('%path%', $this->path)
                    ->toString()
            );
        }
    }
}
