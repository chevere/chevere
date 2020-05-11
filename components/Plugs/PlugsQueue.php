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
use Chevere\Components\Plugs\Interfaces\PlugTypeInterface;
use Ds\Set;
use LogicException;

final class PlugsQueue
{
    private PlugTypeInterface $plugType;

    private array $array = [];

    private Set $set;

    public function __construct(PlugTypeInterface $plugType)
    {
        $this->plugType = $plugType;
        $this->set = new Set;
    }

    public function withAddedPlug(PlugInterface $plug): PlugsQueue
    {
        $this->assertInterface($plug);
        $plugName = get_class($plug);
        $this->assertUnique($plugName);
        $new = clone $this;
        $new->array[$plug->for()][(string) $plug->priority()][] = $plugName;
        $new->set->add($plugName);

        return $new;
    }

    public function plugType(): PlugTypeInterface
    {
        return $this->plugType;
    }

    /**
     * @return array [for => [priority => plugName,],]
     */
    public function toArray(): array
    {
        return $this->array;
    }

    private function assertUnique(string $plugName): void
    {
        if ($this->set->contains($plugName)) {
            throw new LogicException(
                (new Message('Plug %provided% is already registered'))
                    ->code('%provided%', $plugName)
                    ->toString()
            );
        }
    }

    private function assertInterface(PlugInterface $plug): void
    {
        $instanceof = $this->plugType->interface();
        if (!($plug instanceof $instanceof)) {
            throw new LogicException(
                (new Message("Plug %provided% doesn't implements the %expected% interface"))
                    ->code('%provided%', get_class($plug))
                    ->code('%expected%', $this->plugType->interface())
                    ->toString()
            );
        }
    }
}
