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
use Chevere\Components\Events\Interfaces\EventListenerInterface;
use Chevere\Components\Hooks\Interfaces\HookableInterface;
use Chevere\Components\Hooks\Interfaces\HookInterface;
use Chevere\Components\Message\Message;
use Chevere\Components\Plugs\Interfaces\PlugInterface;
use Chevere\Components\Plugs\Interfaces\PlugTypeInterface;
use Chevere\Components\Plugs\Types\EventListenerPlugType;
use Chevere\Components\Plugs\Types\HookPlugType;
use LogicException;

final class PlugDetect
{
    private PlugTypeInterface $type;

    public function __construct(PlugInterface $plug)
    {
        $accept = $this->accept();
        /**
         * @var string $plugInterface
         * @var PlugTypeInterface $plugType
         */
        foreach ($accept as $plugInterface => $plugType) {
            if (is_a($plug, $plugInterface, true)) {
                $this->type = $plugType;
                break;
            }
        }
        if (!isset($this->type)) {
            throw new LogicException(
                (new Message("Plug %className% doesn't implement any of the accepted plug interfaces %interfaces%"))
                    ->code('%className%', $plug->at())
                    ->code('%interfaces%', implode(',', array_keys($accept)))
                    ->toString()
            );
        }
    }

    private function accept(): array
    {
        return [
            HookInterface::class => new HookPlugType,
            EventListenerInterface::class => new EventListenerPlugType,
        ];
    }

    public function type(): PlugTypeInterface
    {
        return $this->type;
    }
}
