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

namespace Chevere\Components\Plugin;

use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\OverflowException;
use Chevere\Interfaces\Plugin\PluggableAnchorsInterface;
use Ds\Set;
use function DeepCopy\deep_copy;

final class PluggableAnchors implements PluggableAnchorsInterface
{
    private Set $set;

    public function __construct()
    {
        $this->set = new Set();
    }

    public function withAdded(string $anchor): PluggableAnchorsInterface
    {
        if ($this->has($anchor)) {
            throw new OverflowException(
                (new Message('Anchor %anchor% has been already added'))
                    ->code('%anchor%', $anchor)
            );
        }
        $new = clone $this;
        $new->set->add($anchor);

        return $new;
    }

    public function has(string $anchor): bool
    {
        return $this->set->contains($anchor);
    }

    public function clonedSet(): Set
    {
        return deep_copy($this->set);
    }
}
