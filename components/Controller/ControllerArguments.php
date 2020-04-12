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
     * @throws LogicException
     */
    public function __construct(array $array)
    {
        $this->assertArray($array);
        $this->map = new Map($array);
    }

    public function hasKey(string $name): bool
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

    private function assertArray(array $array): void
    {
        $pos = -1;
        foreach ($array as $key => $value) {
            $pos++;
            $keyType = gettype($key);
            if ($keyType !== 'string') {
                throw new LogicException(
                    (new Message('Expecting %expected% type keys, type %gettype% provided at index position %pos%'))
                        ->code('%expected%', 'string')
                        ->code('%gettype%', $keyType)
                        ->code('%pos%', (string) $pos)
                        ->toString()
                );
            }
            $valType = gettype($value);
            if ($valType !== 'string') {
                throw new LogicException(
                    (new Message('Expecting %expected% type values, type %gettype% provided at key %name%'))
                        ->code('%expected%', 'string')
                        ->code('%gettype%', $valType)
                        ->code('%name%', $key)
                        ->toString()
                );
            }
        }
    }
}
