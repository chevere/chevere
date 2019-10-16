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
use Chevere\Contracts\Route\PathUriContract;

use function ChevereFn\stringStartsWith;

final class PathUri implements PathUriContract
{
    /** @var string */
    private $path;

    /** @var bool */
    private $hasHandlebars;

    public function __construct(string $path)
    {
        $this->path = $path;
        $this->assertPath();
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

    private function assertPath(): void
    {
        $messages = [];
        if (!stringStartsWith('/', $this->path)) {
            $messages[] = 'must start with a forward slash';
        }
        $illegal = [
            '//' => 'extra-slashes',
            '\\' => 'backslash',
            '{{' => 'double-braces',
            '}}' => 'double-braces',
            ' ' => 'whitespace'
        ];
        $illegals = [];
        foreach ($illegal as $character => $name) {
            if (false !== strpos($this->path, $character)) {
                $illegals[] = (new Message('%character% %name%'))
                    ->code('%character%', $character)
                    ->strtr('%name%', $name)
                    ->toString();
            }
        }
        if (!empty($illegals)) {
            $messages[] = 'must not contain illegal characters (' . implode(', ', $illegals) . ')';
        }
        if (!empty($messages)) {
            throw new InvalidArgumentException(
                (new Message('Route path %path% ' . implode(' and ', $messages)))
                    ->code('%path%', $this->path)
                    ->toString()
            );
        }
    }

    private function assertReservedWildcards(): void
    {
        if (!(preg_match_all('/{([0-9]+)}/', $this->path) === 0)) {
            throw new InvalidArgumentException(
                (new Message('Wildcards in the form of %form% are reserved'))
                    ->code('%form%', '/{n}')
                    ->toString()
            );
        }
    }

    private function getHasHandlebars(): bool
    {
        return false !== strpos($this->path, '{') || false !== strpos($this->path, '}');
    }
}
