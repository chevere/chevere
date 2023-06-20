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

use Chevere\Filesystem\Interfaces\FilenameInterface;
use Chevere\String\StringAssert;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Throwable\Exceptions\LengthException;
use Throwable;
use function Chevere\Message\message;

final class Filename implements FilenameInterface
{
    private string $extension;

    private string $name;

    public function __construct(
        private string $filename
    ) {
        $this->assertBasename();
        $this->extension = pathinfo($this->filename, PATHINFO_EXTENSION);
        $this->name = pathinfo($this->filename, PATHINFO_FILENAME);
    }

    public function __toString(): string
    {
        return $this->filename;
    }

    public function extension(): string
    {
        return $this->extension;
    }

    public function name(): string
    {
        return $this->name;
    }

    private function assertBasename(): void
    {
        try {
            (new StringAssert($this->filename))
                ->notEmpty()
                ->notCtypeSpace();
        } catch (Throwable $e) {
            throw new InvalidArgumentException(previous: $e, code: 100);
        }
        if (strlen($this->filename) > self::MAX_LENGTH_BYTES) {
            throw new LengthException(
                message: message('String %string% provided exceed the limit of %bytes% bytes')
                    ->withCode('%string%', $this->filename)
                    ->withCode('%bytes%', (string) self::MAX_LENGTH_BYTES),
                code: 110
            );
        }
    }
}
