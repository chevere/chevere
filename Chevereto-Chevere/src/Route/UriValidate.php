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

namespace Chevere\Route;

use InvalidArgumentException;
use Chevere\Message;
use Chevere\Utility\Str;

final class UriValidate
{
    /** @var string */
    private $uri;

    /** @var bool */
    private $hasHandlebars;

    public function __construct(string $uri)
    {
        $this->setKey($uri);
        $this->setHasHandlebars();
        if ($this->hasHandlebars) {
            $this->validateReservedWildcards();
        }
    }

    public function uri(): string
    {
        return $this->uri;
    }

    public function hasHandlebars(): bool
    {
        return $this->hasHandlebars;
    }

    private function setKey(string $uri): void
    {
        if (!$this->validateFormat($uri)) {
            throw new InvalidArgumentException(
                (new Message("String %s must start with a forward slash, it shouldn't contain neither whitespace, backslashes or extra forward slashes and it should be specified without a trailing slash."))
                    ->code('%s', $uri)
                    ->toString()
            );
        }
        $this->uri = $uri;
    }

    private function validateFormat(string $uri): bool
    {
        if ('/' == $uri) {
            return true;
        }

        return strlen($uri) > 0 && Str::startsWith('/', $uri)
            && $this->validateFormatSlashes($uri);
    }

    private function validateFormatSlashes(string $uri): bool
    {
        return !Str::endsWith('/', $uri)
            && !Str::contains('//', $uri)
            && !Str::contains(' ', $uri)
            && !Str::contains('\\', $uri);
    }

    private function validateReservedWildcards(): void
    {
        if (!(preg_match_all('/{([0-9]+)}/', $this->uri) === 0)) {
            throw new InvalidArgumentException(
                (new Message('Wildcards in the form of %s are reserved.'))
                    ->code('%s', '/{n}')
                    ->toString()
            );
        }
    }

    private function setHasHandlebars()
    {
        $this->hasHandlebars = Str::contains('{', $this->uri) || Str::contains('}', $this->uri);
    }
}
