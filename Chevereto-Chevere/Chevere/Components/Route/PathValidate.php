<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\Route;

use InvalidArgumentException;

use Chevere\Components\Message\Message;
use Chevere\Contracts\Route\PathValidateContract;

use function ChevereFn\stringEndsWith;
use function ChevereFn\stringStartsWith;

final class PathValidate implements PathValidateContract
{
    /** @var string */
    private $path;

    /** @var bool */
    private $hasHandlebars;

    public function __construct(string $path)
    {
        $this->assertPath($path);
        $this->path = $path;
        $this->hasHandlebars = $this->getHasHandlebars();
        if ($this->hasHandlebars) {
            $this->assertReservedWildcards();
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

    private function assertPath(string $path): void
    {
        if (!$this->validateFormat($path)) {
            throw new InvalidArgumentException(
                (new Message("String %s must start with a forward slash, it shouldn't contain neither whitespace, backslashes or extra forward slashes and it should be specified without a trailing slash."))
                    ->code('%s', $path)
                    ->toString()
            );
        }
    }

    private function validateFormat(string $path): bool
    {
        if ('/' == $path) {
            return true;
        }

        return strlen($path) > 0 && stringStartsWith('/', $path)
            && $this->validateFormatSlashes($path);
    }

    private function validateFormatSlashes(string $path): bool
    {
        return !stringEndsWith('/', $path)
            && false === strpos($path, '//')
            && false === strpos($path, ' ')
            && false === strpos($path, '\\');
    }

    private function assertReservedWildcards(): void
    {
        if (!(preg_match_all('/{([0-9]+)}/', $this->path) === 0)) {
            throw new InvalidArgumentException(
                (new Message('Wildcards in the form of %s are reserved.'))
                    ->code('%s', '/{n}')
                    ->toString()
            );
        }
    }

    private function getHasHandlebars(): bool
    {
        return false !== strpos($this->path, '{') || false !== strpos($this->path, '}');
    }
}
