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

namespace Chevere\Tests\Plugin\Plugs\EventListeners\_resources;

use Chevere\Components\Plugin\PluggableAnchors;
use Chevere\Components\Plugin\Plugs\EventListeners\Traits\PluggableEventsTrait;
use Chevere\Interfaces\Plugin\PluggableAnchorsInterface;
use Chevere\Interfaces\Plugin\Plugs\EventListener\PluggableEventsInterface;

class TestEventable implements PluggableEventsInterface
{
    use PluggableEventsTrait;

    private string $string;

    public static function getEventAnchors(): PluggableAnchorsInterface
    {
        return (new PluggableAnchors)
            ->withAddedAnchor('construct:before')
            ->withAddedAnchor('setString:after');
    }

    public function __construct()
    {
        $string = '';
        $this->event('construct:before', [$string]);

        $this->string = $string;
    }

    public function setString(string $string): void
    {
        $this->string = $string;
        $this->event('setString:after', [$string]);
        $this->string = $string;
    }

    public function string(): string
    {
        return $this->string;
    }
}
