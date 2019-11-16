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

namespace Chevere\Contracts\Route;

use Chevere\Components\Route\Exceptions\WildcardNotFoundException;
use Chevere\Components\Route\Exceptions\WildcardDuplicatedException;

interface PathUriWildcardsContract
{
    /** string Regex pattern used to catch {wildcard} */
    const REGEX_WILDCARD_SEARCH = '/{' . WildcardContract::ACCEPT_CHARS . '}/i';

    /**
     * Creates a new instance.
     *
     * @throws WildcardNotFoundException   if $pathUri doesn't contain any wildcard
     * @throws WildcardDuplicatedException if $pathUri contains a duplicated wildcard
     */
    public function __construct(PathUriContract $pathUri);

    /**
     * Provides access to path key set representation ({wildcards} replaced by {n}).
     */
    public function key(): string;

    /**
     * Provides access to the wildcards array.
     */
    public function wildcards(): array;
}
