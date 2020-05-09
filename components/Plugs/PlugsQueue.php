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

namespace Chevere\Components\Plugs;

use Chevere\Components\Message\Message;
use Chevere\Components\Plugs\Interfaces\PlugInterface;
use Ds\Set;
use LogicException;

final class PlugsQueue
{
    private array $array = [];

    private Set $set;

    public function __construct()
    {
        $this->set = new Set;
    }

    public function withAddedPlug(PlugInterface $plug): PlugsQueue
    {
        $plugName = get_class($plug);
        if ($this->set->contains($plugName)) {
            throw new LogicException(
                (new Message('%plugName% is already registered'))
                    ->code('%plugName%', $plugName)
                    ->toString()
            );
        }
        $new = clone $this;
        $new->array[$plug->for()][(string) $plug->priority()][] = $plugName;
        $new->set->add($plugName);

        return $new;
    }

    /**
     * @return array [for => [priority => plugName,],]
     */
    public function toArray(): array
    {
        return $this->array;
    }
}
