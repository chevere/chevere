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
use function Chevere\Message\message;

final class AssertPathFormat implements AssertPathFormatInterface
{
    private string $driveLetter = '';

    public function __construct(
        private string $path
    ) {
        $this->path = str_replace('\\', '/', $path);
        $this->driveLetter = $this->getDriveLetter();
        if ($this->driveLetter !== '') {
            $this->path = $this->driveLetter . substr($this->path, 1);
        }
        $this->assertAbsolutePath();
        $this->assertNoDoubleDots();
        $this->assertNoDots();
        $this->assertNoExtraSlashes();
    }

    public function path(): string
    {
        return $this->path;
    }

    public function driveLetter(): string
    {
        return $this->driveLetter;
    }

    private function getDriveLetter(): string
    {
        return (strlen($this->path) >= 3
            && ':' === $this->path[1]
            && '/' === $this->path[2]
            && ctype_alpha($this->path[0])
        )
            ? strtoupper($this->path[0])
            : '';
    }

    private function assertAbsolutePath(): void
    {
        if ($this->driveLetter === '' && !str_starts_with($this->path, '/')) {
            throw new PathNotAbsoluteException(
                message('Path %path% must start with %char%')
                    ->withCode('%path%', $this->path)
                    ->withCode('%char%', '/')
            );
        }
    }

    private function assertNoDoubleDots(): void
    {
        if (strpos($this->path, '../') !== false) {
            throw new PathDoubleDotsDashException(
                message('Must omit %chars% for path %path%')
                    ->withCode('%chars%', '../')
                    ->withCode('%path%', $this->path)
            );
        }
    }

    private function assertNoDots(): void
    {
        if (strpos($this->path, './') !== false) {
            throw new PathDotSlashException(
                message('Must omit %chars% for path %path%')
                    ->withCode('%chars%', './')
                    ->withCode('%path%', $this->path)
            );
        }
    }

    private function assertNoExtraSlashes(): void
    {
        if (strpos($this->path, '//') !== false) {
            throw new PathExtraSlashesException(
                message('Path %path% contains extra-slashes')
                    ->withCode('%path%', $this->path)
            );
        }
    }
}
