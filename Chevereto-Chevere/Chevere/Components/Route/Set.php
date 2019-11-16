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

use Chevere\Components\Folder\Exceptions\WildcardDuplicatedException;
use Chevere\Components\Message\Message;
use Chevere\Components\Route\Exceptions\PathUriInvalidException;
use Chevere\Components\Route\Exceptions\WildcardNotFoundException;
use Chevere\Contracts\Route\PathUriContract;
use Chevere\Contracts\Route\SetContract;
use function ChevereFn\stringReplaceFirst;

final class Set implements SetContract
{
    /** @var PathUriContract The route path */
    private $pathUri;

    /** @var string Path key set representation ({wildcards} replaced by {n}) */
    private $key;

    /** @var array */
    private $matches;

    /** @var array string[] */
    private $wildcards;

    /**
     * {@inheritdoc}
     */
    public function __construct(PathUriContract $pathUri)
    {
        $this->pathUri = $pathUri;
        $this->assertHasHandlebars();
        // $matches[0] => [{wildcard1}, {wildcard2},...]
        // $matches[1] => [wildcard1, wildcard2,...]
        if (!preg_match_all(SetContract::REGEX_WILDCARD_SEARCH, $this->pathUri->path(), $matches)) {
            throw new WildcardNotFoundException(
                (new Message("Path uri %path% doesn't contain any wildcard"))
                    ->code('%path%', $this->pathUri->path())
                    ->toString()
            );
        }
        $this->matches = $matches;
        $this->handleSetKey();
    }

    /**
     * {@inheritdoc}
     */
    public function key(): string
    {
        return $this->key;
    }

    /**
     * {@inheritdoc}
     */
    public function matches(): array
    {
        return $this->matches ?? [];
    }

    /**
     * {@inheritdoc}
     */
    public function wildcards(): array
    {
        return $this->wildcards;
    }

    private function assertHasHandlebars(): void
    {
        if (!$this->pathUri->hasHandlebars()) {
            throw new PathUriInvalidException(
                (new Message("Path uri %path% doesn't contain any wildcard"))
                    ->code('%path%', $this->pathUri->path())
                    ->toString()
            );
        }
    }

    private function handleSetKey(): void
    {
        $this->key = $this->pathUri->path();
        foreach ($this->matches[0] as $key => $val) {
            // Change {wildcard} to {n} (n is the wildcard index)
            if (isset($this->key)) {
                $this->key = stringReplaceFirst($val, "{{$key}}", $this->key);
            }
            $wildcard = $this->matches[1][$key];
            if (in_array($wildcard, $this->wildcards ?? [])) {
                throw new WildcardDuplicatedException(
                    (new Message('Duplicated wildcard %wildcard% in path uri %path%'))
                        ->code('%wildcard%', $this->matches[0][$key])
                        ->code('%path%', $this->path)
                        ->toString()
                );
            }
            $this->wildcards[] = $wildcard;
        }
    }
}
