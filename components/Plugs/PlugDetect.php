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

use Chevere\Components\Events\Interfaces\EventableInterface;
use Chevere\Components\Hooks\Interfaces\HookableInterface;
use Chevere\Components\Message\Message;
use Chevere\Components\Plugs\Interfaces\PlugInterface;
use Chevere\Components\Plugs\Interfaces\PlugTypeInterface;
use LogicException;

final class PlugDetect
{
    private PlugTypeInterface $type;

    public function __construct(PlugInterface $plug)
    {
        $at = $plug->at();
        /**
         * @var PlugableAnchors $anchors
         */
        if (is_a($at, HookableInterface::class, true)) {
            $this->type = new PlugTypeHook;
        } elseif (is_a($at, EventableInterface::class, true)) {
            $this->type = new PlugTypeEventListener;
        }
        if (!isset($this->type)) {
            throw new LogicException(
                (new Message('Unknown plug %className%'))
                    ->code('%className%', $at)
                    ->toString()
            );
        }
    }

    public function type(): PlugTypeInterface
    {
        return $this->type;
    }
}
