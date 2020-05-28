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
use Chevere\Exceptions\Filesystem\PathDotSlashException;
use Chevere\Exceptions\Filesystem\PathDoubleDotsDashException;
use Chevere\Exceptions\Filesystem\PathExtraSlashesException;
use Chevere\Exceptions\Filesystem\PathNotAbsoluteException;
use Chevere\Exceptions\Filesystem\PathNotExistsException;
use Chevere\Exceptions\Filesystem\PathUnableToChmodException;
use Chevere\Interfaces\Filesystem\PathInterface;

final class Path implements PathInterface
{
    /** @var string Absolute path */
    private string $absolute;

    /**
     * @throws PathNotAbsoluteException
     * @throws PathDoubleDotsDashException
     * @throws PathDotSlashException
     * @throws PathExtraSlashesException
     */
    public function __construct(string $absolute)
    {
        $assert = new AssertPathFormat($absolute);
        $this->absolute = $assert->path();
    }

    public function absolute(): string
    {
        return $this->absolute;
    }

    public function exists(): bool
    {
        $this->clearStatCache();

        return false !== stream_resolve_include_path($this->absolute);
    }

    public function isDir(): bool
    {
        $this->clearStatCache();

        return is_dir($this->absolute);
    }

    public function isFile(): bool
    {
        $this->clearStatCache();

        return is_file($this->absolute);
    }

    /**
     * @codeCoverageIgnore
     * @throws PathNotExistsException
     * @throws PathUnableToChmodException
     */
    public function chmod(int $mode): void
    {
        $this->assertExists();
        if (chmod($this->absolute, $mode) === false) {
            throw new PathUnableToChmodException(
                (new Message('Unable to chmod %mode% %path%'))
                    ->strong('%mode%', (string) $mode)
                    ->code('%path%', $this->absolute)
            );
        }
    }

    /**
     * @codeCoverageIgnore
     * @throws PathNotExistsException
     */
    public function isWritable(): bool
    {
        $this->assertExists();

        return is_writable($this->absolute);
    }

    /**
     * @codeCoverageIgnore
     * @throws PathNotExistsException
     */
    public function isReadable(): bool
    {
        $this->assertExists();

        return is_readable($this->absolute);
    }

    public function getChild(string $child): PathInterface
    {
        $parent = $this->absolute;
        $childPath = rtrim($parent, '/');

        return new Path($childPath . '/' . $child);
    }

    private function assertExists(): void
    {
        if ($this->exists() === false) {
            throw new PathNotExistsException(
                (new Message("Path %path% doesn't exists"))
                    ->code('%path%', $this->absolute)
            );
        }
    }

    private function clearStatCache(): void
    {
        clearstatcache(true, $this->absolute);
    }
}
