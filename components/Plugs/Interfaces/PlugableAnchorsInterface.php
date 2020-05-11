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

namespace Chevere\Components\Plugs\Interfaces;

use Chevere\Components\Plugs\Exceptions\PlugableAnchorExistsException;
use Ds\Set;

interface PlugableAnchorsInterface
{
    /**
     * @throws PlugableAnchorExistsException
     */
    public function withAddedAnchor(string $anchor): PlugableAnchorsInterface;

    public function has(string $anchor): bool;

    public function set(): Set;
}
