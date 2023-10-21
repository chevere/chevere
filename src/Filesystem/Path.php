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

use Chevere\Filesystem\Exceptions\FilesystemException;
use Chevere\Filesystem\Exceptions\PathNotExistsException;
use Chevere\Filesystem\Exceptions\PathUnableToChmodException;
use Chevere\Filesystem\Interfaces\PathInterface;
use Throwable;
use function Chevere\Message\message;
use function Safe\fclose;
use function Safe\fopen;
use function Safe\fwrite;
use function Safe\unlink;

final class Path implements PathInterface
{
    private string $absolute;

    public function __construct(string $absolute)
    {
        $absolute = resolvePath($absolute);
        $assert = new AssertPathFormat($absolute);
        $this->absolute = $assert->path();
    }

    public function __toString(): string
    {
        return $this->absolute;
    }

    public function exists(): bool
    {
        // @infection-ignore-all
        $this->clearStatCache();

        return file_exists($this->absolute) !== false;
    }

    public function assertExists(): void
    {
        if (! $this->exists()) {
            throw new PathNotExistsException(
                message("Path %path% doesn't exists")
                    ->withCode('%path%', $this->absolute)
            );
        }
    }

    public function isDirectory(): bool
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
        if (! chmod($this->absolute, $mode)) {
            throw new PathUnableToChmodException(
                message('Unable to chmod %mode% %path%')
                    ->withStrong('%mode%', (string) $mode)
                    ->withCode('%path%', $this->absolute)
            );
        }
    }

    public function isWritable(): bool
    {
        $this->assertExists();
        // @codeCoverageIgnoreStart
        if (is_writable($this->absolute)) {
            return true;
        }
        $testFile = sprintf('%s/%s.tmp', $this->absolute, uniqid('data_write_test_'));

        // @infection-ignore-all
        try {
            $handle = fopen($testFile, 'w');
            if (! is_resource($handle) || fwrite($handle, 't') === 0) {
                return false;
            }
            fclose($handle);
            unlink($testFile);

            return true;
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
