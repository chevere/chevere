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

namespace Chevere\Components\Plugs\Types;

use Chevere\Components\Message\Message;
use Chevere\Components\Plugs\Interfaces\PlugTypeInterface;
use Chevere\Components\Plugs\Interfaces\PlugTypesListInterface;
use Ds\Map;
use Generator;
use RuntimeException;

final class PlugTypesList implements PlugTypesListInterface
{
    private Map $map;

    public function __construct()
    {
        $path = __DIR__ . '/list.php';
        $list = include $path;
        $this->map = new Map($list);
        foreach ($this->map->pairs() as $pair) {
            // @codeCoverageIgnoreStart
            if (!($pair->value instanceof PlugTypeInterface)) {
                throw new RuntimeException(
                    (new Message('List source (%path%) contains an invalid type not implementing %interface% at %pos% index'))
                        ->code('%path%', $path)
                        ->code('%interface%', PlugTypeInterface::class)
                        ->code('%pos%', (string) $pair->key)
                        ->toString()
                );
            }
            // @codeCoverageIgnoreEnd
        }
    }

    public function toArray(): array
    {
        return $this->map->toArray();
    }

    public function getGenerator(): Generator
    {
        foreach ($this->map->pairs() as $pair) {
            yield $pair->key => $pair->value;
        }
    }
}
