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
use Chevere\Components\Str\StrAssert;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Core\LengthException;
use Chevere\Interfaces\Filesystem\FilenameInterface;
use Throwable;

final class Filename implements FilenameInterface
{
    private string $extension;

    private string $name;

    public function __construct(
        private string $basename
    ) {
        $this->assertBasename();
        $this->extension = pathinfo($this->basename, PATHINFO_EXTENSION);
        $this->name = pathinfo($this->basename, PATHINFO_FILENAME);
    }

    public function toString(): string
    {
        return $this->basename;
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
            (new StrAssert($this->basename))
                ->notEmpty()
                ->notCtypeSpace();
        } catch (Throwable $e) {
            throw new InvalidArgumentException(code: 100);
        }
        if (mb_strlen($this->basename) > self::MAX_LENGTH_BYTES) {
            throw new LengthException(
                message: (new Message('String %string% provided exceed the limit of %bytes% bytes'))
                    ->code('%string%', $this->basename)
                    ->code('%bytes%', (string) self::MAX_LENGTH_BYTES),
                code: 110
            );
        }
    }
}
