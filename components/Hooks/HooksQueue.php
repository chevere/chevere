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

namespace Chevere\Components\Hooks;

use Chevere\Components\Hooks\Interfaces\HookInterface;
use Chevere\Components\Message\Message;
use Ds\Set;
use LogicException;

final class HooksQueue
{
    private array $array = [];

    private Set $set;

    public function __construct()
    {
        $this->set = new Set;
    }

    public function withHook(HookInterface $hook): HooksQueue
    {
        $hookName = get_class($hook);
        if ($this->set->contains($hookName)) {
            throw new LogicException(
                (new Message('Hook %hook% is already registered'))
                    ->code('%hook%', $hookName)
                    ->toString()
            );
        }
        new AssertHook($hook);
        $anchor = $hook->anchor();
        $priority = (string) $hook->priority();
        $new = clone $this;
        $new->array[$anchor][$priority][] = $hookName;
        $new->set->add($hookName);

        return $new;
    }

    /**
     * @return array [anchor => [0 => HookName,],]
     */
    public function toArray(): array
    {
        return $this->array;
    }
}
