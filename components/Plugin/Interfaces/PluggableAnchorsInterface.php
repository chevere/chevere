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

namespace Chevere\Components\Plugin\Interfaces;

use Chevere\Components\Plugin\Exceptions\PluggableAnchorExistsException;
use Ds\Set;

interface PluggableAnchorsInterface
{
    /**
     * @throws PluggableAnchorExistsException
     */
    public function withAddedAnchor(string $anchor): PluggableAnchorsInterface;

    public function has(string $anchor): bool;

    public function set(): Set;
}
