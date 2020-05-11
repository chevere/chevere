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
use Chevere\Components\Plugs\Exceptions\PlugableAnchorExistsException;
use Chevere\Components\Plugs\Interfaces\PlugableAnchorsInterface;
use Ds\Set;
use function DeepCopy\deep_copy;

final class PlugableAnchors implements PlugableAnchorsInterface
{
    private Set $set;

    public function __construct()
    {
        $this->set = new Set;
    }

    public function withAddedAnchor(string $anchor): PlugableAnchorsInterface
    {
        if ($this->has($anchor)) {
            throw new PlugableAnchorExistsException(
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

    public function set(): Set
    {
        return deep_copy($this->set);
    }
}
