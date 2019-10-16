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
use LogicException;

use Chevere\Components\Message\Message;

use function ChevereFn\stringReplaceFirst;

final class Set
{
    /** @const string Regex pattern used to catch {wildcard}. */
    const REGEX_WILDCARD_SEARCH = '/{([a-z\_][\w_]*?)}/i';

    /** @var string The route path */
    private $path;

    /** @var string Path key set representation ({wildcards} replaced by {n}) */
    private $key;

    /** @var array */
    private $matches;

    /** @var array string[] */
    private $wildcards;

    public function __construct(string $path)
    {
        $this->path = $path;
        // $matches[0] => [{wildcard1}, {wildcard2},...]
        // $matches[1] => [wildcard1, wildcard2,...]
        if (!preg_match_all(static::REGEX_WILDCARD_SEARCH, $this->path, $matches)) {
            throw new InvalidArgumentException(
                (new Message('Expression %path% '))
                    ->toString()
            );
        }
        $this->matches = $matches;
        $this->key = $path;
        $this->handleMatches();
    }

    public function key(): string
    {
        return $this->key;
    }

    public function matches(): array
    {
        return $this->matches ?? [];
    }

    public function toArray(): array
    {
        return $this->wildcards ?? [];
    }

    private function handleMatches(): void
    {
        foreach ($this->matches[0] as $k => $v) {
            // Change {wildcard} to {n} (n is the wildcard index)
            if (isset($this->key)) {
                $this->key = stringReplaceFirst($v, "{{$k}}", $this->key);
            }
            $wildcard = $this->matches[1][$k];
            if (in_array($wildcard, $this->wildcards ?? [])) {
                throw new LogicException(
                    (new Message('Must declare one unique wildcard per capturing group, duplicated %s detected in route %r'))
                        ->code('%s', $this->matches[0][$k])
                        ->code('%r', $this->path)
                        ->toString()
                );
            }
            $this->wildcards[] = $wildcard;
        }
    }
}
