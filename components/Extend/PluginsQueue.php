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

namespace Chevere\Components\Extend;

use Chevere\Components\Extend\AssertPlugin;
use Chevere\Components\Extend\Interfaces\PluginInterface;
use Chevere\Components\Message\Message;
use Ds\Set;
use LogicException;

final class PluginsQueue
{
    private array $array = [];

    private Set $set;

    public function __construct()
    {
        $this->set = new Set;
    }

    public function withPlugin(PluginInterface $plugin): PluginsQueue
    {
        $pluginName = get_class($plugin);
        if ($this->set->contains($pluginName)) {
            throw new LogicException(
                (new Message('%pluginName% is already registered'))
                    ->code('%pluginName%', $pluginName)
                    ->toString()
            );
        }
        new AssertPlugin($plugin);
        $for = $plugin->for();
        $priority = (string) $plugin->priority();
        $new = clone $this;
        $new->array[$for][$priority][] = $pluginName;
        $new->set->add($pluginName);

        return $new;
    }

    /**
     * @return array [for => [priority => pluginName,],]
     */
    public function toArray(): array
    {
        return $this->array;
    }
}
