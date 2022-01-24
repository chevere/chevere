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

namespace Chevere\Pluggable;

use Chevere\Message\Message;
use Chevere\Pluggable\Interfaces\PluggableAnchorsInterface;
use Chevere\Throwable\Exceptions\OverflowException;
use function Chevere\VarSupport\deepCopy;
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
        return deepCopy($this->set);
    }
}
