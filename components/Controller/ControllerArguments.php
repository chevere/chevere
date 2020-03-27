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
use Chevere\Components\Message\Message;
use Ds\Map;
use LogicException;
use OutOfBoundsException;

final class ControllerArguments implements ControllerArgumentsInterface
{
    private Map $map;

    /**
     * @param array $map [<string>name => <mixed>value,]
     */
    public function __construct(array $array)
    {
        $this->assertArray($array);
        $this->map = new Map($array);
    }

    public function hasKey(string $name): bool
    {
        /** @var \Ds\TKey $name */
        return $this->map->hasKey($name);
    }

    /**
     * @throws OutOfBoundsException
     */
    public function get(string $name)
    {
        /** @var \Ds\TKey $name */
        return $this->map->get($name);
    }

    private function assertArray(array $array): void
    {
        $pos = -1;
        foreach (array_keys($array) as $key) {
            $pos++;
            $type = gettype($key);
            if ($type !== 'string') {
                throw new LogicException(
                    (new Message('Expecting %expected% type keys, type %gettype% provided at index position %pos%'))
                        ->code('%expected%', 'string')
                        ->code('%gettype%', $type)
                        ->code('%pos%', (string) $pos)
                        ->toString()
                );
            }
        }
    }
}
