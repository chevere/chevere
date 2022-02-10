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

namespace Chevere\Filesystem;

use Chevere\Filesystem\Exceptions\PathDotSlashException;
use Chevere\Filesystem\Exceptions\PathDoubleDotsDashException;
use Chevere\Filesystem\Exceptions\PathExtraSlashesException;
use Chevere\Filesystem\Exceptions\PathNotAbsoluteException;
use Chevere\Filesystem\Interfaces\AssertPathFormatInterface;
use Chevere\Message\Message;

final class AssertPathFormat implements AssertPathFormatInterface
{
    public function __construct(
        private string $path
    ) {
        $this->assertAbsolutePath();
        $this->assertNoDoubleDots();
        $this->assertNoDots();
        $this->assertNoExtraSlashes();
    }

    public function path(): string
    {
        return $this->path;
    }

    private function assertAbsolutePath(): void
    {
        if (!str_starts_with($this->path, '/')) {
            throw new PathNotAbsoluteException(
                (new Message('Path %path% must start with %char%'))
                    ->code('%path%', $this->path)
                    ->code('%char%', '/')
            );
        }
    }

    private function assertNoDoubleDots(): void
    {
        if (strpos($this->path, '../') !== false) {
            throw new PathDoubleDotsDashException(
                (new Message('Must omit %chars% for path %path%'))
                    ->code('%chars%', '../')
                    ->code('%path%', $this->path)
            );
        }
    }

    private function assertNoDots(): void
    {
        if (strpos($this->path, './') !== false) {
            throw new PathDotSlashException(
                (new Message('Must omit %chars% for path %path%'))
                    ->code('%chars%', './')
                    ->code('%path%', $this->path)
            );
        }
    }

    private function assertNoExtraSlashes(): void
    {
        if (strpos($this->path, '//') !== false) {
            throw new PathExtraSlashesException(
                (new Message('Path %path% contains extra-slashes'))
                    ->code('%path%', $this->path)
            );
        }
    }
}
