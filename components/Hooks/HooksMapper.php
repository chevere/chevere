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
use Chevere\Components\Plugs\AssertPlug;
use Chevere\Components\Plugs\PlugsMapper;

final class HooksMapper
{
    private PlugsMapper $plugsMapper;

    public function __construct()
    {
        $this->plugsMapper = new PlugsMapper;
    }

    public function withAdded(HookInterface $hook): HooksMapper
    {
        $new = clone $this;
        $new->plugsMapper = $new->plugsMapper->withAddedPlug(
            new AssertPlug($hook)
        );

        return $new;
    }
}
