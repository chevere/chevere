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

namespace Chevere\Components\Pluggable;

use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\OverflowException;
use Chevere\Exceptions\Core\TypeException;
use Chevere\Interfaces\Pluggable\PlugInterface;
use Chevere\Interfaces\Pluggable\PlugsQueueInterface;
use Chevere\Interfaces\Pluggable\PlugTypeInterface;
use Ds\Set;

final class PlugsQueue implements PlugsQueueInterface
{
    private array $array = [];

    private Set $set;

    public function __construct(
        private PlugTypeInterface $plugType
    ) {
        $this->set = new Set();
    }

    public function withAdded(PlugInterface $plug): PlugsQueueInterface
    {
        $this->assertInterface($plug);
        $plugName = $plug::class;
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
            throw new OverflowException(
                (new Message('Plug %provided% is already registered'))
                    ->code('%provided%', $plugName)
            );
        }
    }

    private function assertInterface(PlugInterface $plug): void
    {
        $instanceof = $this->plugType->interface();
        if (!($plug instanceof $instanceof)) {
            throw new TypeException(
                (new Message("Plug %provided% doesn't implements the %expected% interface"))
                    ->code('%provided%', $plug::class)
                    ->code('%expected%', $this->plugType->interface())
            );
        }
    }
}
