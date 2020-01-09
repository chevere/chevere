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

use Chevere\Components\App\Exceptions\ResolverException;

interface ResolverContract
{
    public function __construct(ResolvableContract $resolvable);

    /**
     *
     * @return Chevere\Contracts\App\BuilderContract A resolved builder contract
     */
    public function builder(): BuilderContract;
}
