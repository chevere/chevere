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
use Chevere\Message\Message;
use Chevere\Utility\Str;
use Chevere\Contracts\Route\PathValidateContract;

final class PathValidate implements PathValidateContract
{
    /** @var string */
    private $path;

    /** @var bool */
    private $hasHandlebars;

    public function __construct(string $path)
    {
        $this->setPath($path);
        $this->setHasHandlebars();
        if ($this->hasHandlebars) {
            $this->validateReservedWildcards();
        }
    }

    public function path(): string
    {
        return $this->path;
    }

    public function hasHandlebars(): bool
    {
        return $this->hasHandlebars;
    }

    private function setPath(string $path): void
    {
        if (!$this->validateFormat($path)) {
            throw new InvalidArgumentException(
                (new Message("String %s must start with a forward slash, it shouldn't contain neither whitespace, backslashes or extra forward slashes and it should be specified without a trailing slash."))
                    ->code('%s', $path)
                    ->toString()
            );
        }
        $this->path = $path;
    }

    private function validateFormat(string $path): bool
    {
        if ('/' == $path) {
            return true;
        }

        return strlen($path) > 0 && Str::startsWith('/', $path)
            && $this->validateFormatSlashes($path);
    }

    private function validateFormatSlashes(string $path): bool
    {
        return !Str::endsWith('/', $path)
            && !Str::contains('//', $path)
            && !Str::contains(' ', $path)
            && !Str::contains('\\', $path);
    }

    private function validateReservedWildcards(): void
    {
        if (!(preg_match_all('/{([0-9]+)}/', $this->path) === 0)) {
            throw new InvalidArgumentException(
                (new Message('Wildcards in the form of %s are reserved.'))
                    ->code('%s', '/{n}')
                    ->toString()
            );
        }
    }

    private function setHasHandlebars(): void
    {
        $this->hasHandlebars = Str::contains('{', $this->path) || Str::contains('}', $this->path);
    }
}
