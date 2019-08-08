<?php

declare(strict_types=1);

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevere\Route\src;

use InvalidArgumentException;
use Chevere\Message;
use Chevere\Utility\Str;

final class KeyValidation
{
    /** @var string */
    private $key;

    /** @var bool */
    private $hasHandlebars;

    public function __construct(string $key)
    {
        $this->setKey($key);
        $this->setHasHandlebars();
        if ($this->hasHandlebars) {
            $this->validateReservedWildcards();
        }
    }

    public function key(): string
    {
        return $this->key;
    }

    public function hasHandlebars(): bool
    {
        return $this->hasHandlebars;
    }

    private function setKey(string $key): void
    {
        if (!$this->validateFormat($key)) {
            throw new InvalidArgumentException(
                (new Message("String %s must start with a forward slash, it shouldn't contain neither whitespace, backslashes or extra forward slashes and it should be specified without a trailing slash."))
                    ->code('%s', $key)
                    ->toString()
            );
        }
        $this->key = $key;
    }

    private function validateFormat(string $key): bool
    {
        if ('/' == $key) {
            return true;
        }

        return strlen($key) > 0 && Str::startsWith('/', $key)
            && $this->validateFormatSlashes($key);
    }

    private function validateFormatSlashes(string $key): bool
    {
        return !Str::endsWith('/', $key)
            && !Str::contains('//', $key)
            && !Str::contains(' ', $key)
            && !Str::contains('\\', $key);
    }

    private function validateReservedWildcards(): void
    {
        if (!(preg_match_all('/{([0-9]+)}/', $this->key) === 0)) {
            throw new InvalidArgumentException(
                (new Message('Wildcards in the form of %s are reserved.'))
                    ->code('%s', '/{n}')
                    ->toString()
            );
        }
    }

    private function setHasHandlebars()
    {
        $this->hasHandlebars = Str::contains('{', $this->key) || Str::contains('}', $this->key);
    }
}
