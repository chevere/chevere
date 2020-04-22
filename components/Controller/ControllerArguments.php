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

namespace Chevere\Components\Controller;

use Chevere\Components\Controller\Interfaces\ControllerArgumentsInterface;
use Ds\Map;
use OutOfBoundsException;

final class ControllerArguments implements ControllerArgumentsInterface
{
    private Map $map;

    public function __construct()
    {
        $this->map = new Map;
    }

    public function put(string $name, string $value): void
    {
        $this->map->put($name, $value);
    }

    public function has(string $name): bool
    {
        /** @var \Ds\TKey $key */
        $key = $name;

        return $this->map->hasKey($key);
    }

    /**
     * @throws OutOfBoundsException
     */
    public function get(string $name): string
    {
        /**
         * @var \Ds\TKey $name
         * @var string $return
         */
        $return = $this->map->get($name);

        return $return;
    }
}
