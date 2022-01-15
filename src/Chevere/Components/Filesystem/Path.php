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
use Chevere\Exceptions\Filesystem\FilesystemException;
use Chevere\Exceptions\Filesystem\PathNotExistsException;
use Chevere\Exceptions\Filesystem\PathUnableToChmodException;
use Chevere\Interfaces\Filesystem\PathInterface;
use function Safe\fclose;
use function Safe\fopen;
use function Safe\fwrite;
use function Safe\unlink;
use Throwable;

final class Path implements PathInterface
{
    private string $absolute;

    public function __construct(string $absolute)
    {
        $assert = new AssertPathFormat($absolute);
        $this->absolute = $assert->path();
    }

    public function toString(): string
    {
        return $this->absolute;
    }

    public function exists(): bool
    {
        // @infection-ignore-all
        $this->clearStatCache();

        return stream_resolve_include_path($this->absolute) !== false;
    }

    public function assertExists(): void
    {
        if (!$this->exists()) {
            throw new PathNotExistsException(
                (new Message("Path %path% doesn't exists"))
                    ->code('%path%', $this->absolute)
            );
        }
    }

    public function isDir(): bool
    {
        // @infection-ignore-all
        $this->clearStatCache();

        return is_dir($this->absolute);
    }

    public function isFile(): bool
    {
        // @infection-ignore-all
        $this->clearStatCache();

        return is_file($this->absolute);
    }

    /**
     * @codeCoverageIgnore
     * @infection-ignore-all
     */
    public function chmod(int $mode): void
    {
        $this->assertExists();
        if (!chmod($this->absolute, $mode)) {
            throw new PathUnableToChmodException(
                (new Message('Unable to chmod %mode% %path%'))
                    ->strong('%mode%', (string) $mode)
                    ->code('%path%', $this->absolute)
            );
        }
    }

    public function isWritable(): bool
    {
        $this->assertExists();
        if (is_writable($this->absolute)) {
            return true;
        }
        // @codeCoverageIgnoreStart
        $testFile = sprintf('%s/%s.tmp', $this->absolute, uniqid('data_write_test_'));

        // @infection-ignore-all
        try {
            $handle = fopen($testFile, 'w');
            if (!$handle || fwrite($handle, 't') === false) {
                return false;
            }
            fclose($handle);

            return unlink($testFile);
        } catch (Throwable $e) {
            throw new FilesystemException(previous: $e);
        }
        // @codeCoverageIgnoreEnd
    }

    public function isReadable(): bool
    {
        $this->assertExists();

        return is_readable($this->absolute);
    }

    public function getChild(string $path): PathInterface
    {
        $parent = $this->absolute;
        $childPath = rtrim($parent, '/');

        return new self($childPath . '/' . $path);
    }

    /**
     * @infection-ignore-all
     */
    private function clearStatCache(): void
    {
        clearstatcache(true, $this->absolute);
    }
}
