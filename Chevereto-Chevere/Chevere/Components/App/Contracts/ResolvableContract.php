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

namespace Chevere\Components\App\Contracts;

use Chevere\Components\App\Exceptions\RouterRequiredException;
use Chevere\Components\App\Exceptions\RouterCantResolveException;
use Chevere\Components\App\Exceptions\RequestRequiredException;

interface ResolvableContract
{
    /**
     * @throws RequestRequiredException if $builder lacks of a request
     * @throws RouterRequiredException if $builder lacks of a RouterContract
     * @throws RouterCantResolveException if $builder RouterContract lacks of routing
     *
     */
    public function __construct(BuilderContract $builder);

    /**
     *
     * @return Chevere\Contracts\App\BuilderContract A resolvable BuilderContract
     */
    public function builder(): BuilderContract;
}
