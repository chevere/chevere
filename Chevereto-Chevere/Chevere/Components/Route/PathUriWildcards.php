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
use Chevere\Components\Route\Exceptions\WildcardNotFoundException;
use Chevere\Contracts\Route\PathUriContract;
use Chevere\Contracts\Route\PathUriWildcardsContract;
use function ChevereFn\stringReplaceFirst;

/**
 * Detect the wildcards present in a PathUriContract and provide access to it.
 */
final class PathUriWildcards implements PathUriWildcardsContract
{
    /** @var PathUriContract The route path */
    private $pathUri;

    /** @var string Path key set representation ({wildcards} replaced by {n}) */
    private $key;

    /** @var array string[] */
    private $wildcards;

    /**
     * {@inheritdoc}
     */
    public function __construct(PathUriContract $pathUri)
    {
        $this->pathUri = $pathUri;
        $this->assertPathUriHasWildcards();
        $this->handleSetKey();
    }

    /**
     * {@inheritdoc}
     */
    public function pathUri(): PathUriContract
    {
        return $this->pathUri;
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
    public function wildcards(): array
    {
        return $this->wildcards;
    }

    private function assertPathUriHasWildcards(): void
    {
        if (!$this->pathUri->hasWildcards()) {
            throw new WildcardNotFoundException(
                (new Message("Path uri %path% doesn't contain any wildcard delimiter (braced %wildcard%)"))
                    ->code('%path%', $this->pathUri->path())
                    ->code('%wildcard%', '{wildcard}')
                    ->toString()
            );
        }
    }

    private function handleSetKey(): void
    {
        $this->key = $this->pathUri->path();
        foreach ($this->pathUri->wildcardsMatch()[0] as $key => $val) {
            // Change {wildcard} to {n} (n is the wildcard index)
            if (isset($this->key)) {
                $this->key = stringReplaceFirst($val, "{{$key}}", $this->key);
            }
            $wildcard = $this->pathUri->wildcardsMatch()[1][$key];
            if (in_array($wildcard, $this->wildcards ?? [])) {
                throw new WildcardDuplicatedException(
                    (new Message('Duplicated wildcard %wildcard% in path uri %path%'))
                        ->code('%wildcard%', $this->pathUri->wildcardsMatch()[0][$key])
                        ->code('%path%', $this->path)
                        ->toString()
                );
            }
            $this->wildcards[] = $wildcard;
        }
    }
}
