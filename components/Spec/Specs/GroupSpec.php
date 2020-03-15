<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevere.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\Spec;

use Chevere\Components\Common\Interfaces\ToArrayInterface;

final class GroupSpec implements ToArrayInterface
{
    private $array = [];

    public function __construct(RouteEndpointInterface $endpoint, string $spec)
    {
        $this->array = [
            // 'name' => $endpoint->method()->name(),
            'spec' => $spec,
            // 'routes' => $endpoint->method()->description(),
        ];
    }

    public function toArray(): array
    {
        return $this->array;
    }

    // 'name' => 'api',
    // 'spec' => '/spec/api/routes.json',
    // 'routes' => [...]
}
