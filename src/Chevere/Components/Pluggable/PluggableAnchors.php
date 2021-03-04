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
use Chevere\Interfaces\Pluggable\PluggableAnchorsInterface;
use function DeepCopy\deep_copy;
use Ds\Set;

final class PluggableAnchors implements PluggableAnchorsInterface
{
    private Set $set;

    public function __construct(string ...$anchors)
    {
        $this->set = new Set($anchors);
    }

    public function withAdded(string ...$anchors): PluggableAnchorsInterface
    {
        $new = clone $this;
        foreach ($anchors as $anchor) {
            if ($new->has($anchor)) {
                throw new OverflowException(
                    (new Message('Anchor %anchor% has been already added'))
                        ->code('%anchor%', $anchor)
                );
            }
            $new->set->add($anchor);
        }

        return $new;
    }

    public function has(string ...$anchors): bool
    {
        return $this->set->contains(...$anchors);
    }

    public function clonedSet(): Set
    {
        return deep_copy($this->set);
    }
}
