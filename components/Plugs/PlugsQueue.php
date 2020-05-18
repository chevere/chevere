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

use Chevere\Components\Exception\InvalidArgumentException;
use Chevere\Components\Message\Message;
use Chevere\Components\Plugs\Exceptions\PlugInterfaceException;
use Chevere\Components\Plugs\Interfaces\PlugInterface;
use Chevere\Components\Plugs\Interfaces\PlugsQueueInterface;
use Chevere\Components\Plugs\Interfaces\PlugTypeInterface;
use Ds\Set;

final class PlugsQueue implements PlugsQueueInterface
{
    private array $array = [];

    private PlugTypeInterface $plugType;

    private Set $set;

    public function __construct(PlugTypeInterface $plugType)
    {
        $this->plugType = $plugType;
        $this->set = new Set;
    }

    public function withAddedPlug(PlugInterface $plug): PlugsQueueInterface
    {
        $this->assertInterface($plug);
        $plugName = get_class($plug);
        $this->assertUnique($plugName);
        $new = clone $this;
        $new->array[$plug->anchor()][(string) $plug->priority()][] = $plugName;
        $new->set->add($plugName);

        return $new;
    }

    public function plugType(): PlugTypeInterface
    {
        return $this->plugType;
    }

    public function toArray(): array
    {
        return $this->array;
    }

    private function assertUnique(string $plugName): void
    {
        if ($this->set->contains($plugName)) {
            throw new InvalidArgumentException(
                (new Message('Plug %provided% is already registered'))
                    ->code('%provided%', $plugName)
            );
        }
    }

    private function assertInterface(PlugInterface $plug): void
    {
        $instanceof = $this->plugType->interface();
        if (!($plug instanceof $instanceof)) {
            throw new PlugInterfaceException(
                (new Message("Plug %provided% doesn't implements the %expected% interface"))
                    ->code('%provided%', get_class($plug))
                    ->code('%expected%', $this->plugType->interface())
            );
        }
    }
}
