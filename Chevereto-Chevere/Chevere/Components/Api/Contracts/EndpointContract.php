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

namespace Chevere\Components\Api\Contracts;

use Chevere\Components\Http\Contracts\MethodControllerNameCollectionContract;

interface EndpointContract
{
    public function __construct(MethodControllerNameCollectionContract $collection);

    public function methodControllerNameCollection(): MethodControllerNameCollectionContract;

    public function toArray(): array;
}
