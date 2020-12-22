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

namespace Chevere\Tests\Pluggable\Plug\Event\_resources;

use Chevere\Components\Pluggable\Plug\Event\Traits\PluggableEventsTrait;
use Chevere\Components\Pluggable\PluggableAnchors;
use Chevere\Interfaces\Pluggable\Plug\Event\PluggableEventsInterface;
use Chevere\Interfaces\Pluggable\PluggableAnchorsInterface;

class TestEventable implements PluggableEventsInterface
{
    use PluggableEventsTrait;

    private string $string;

    public function __construct()
    {
        $string = '';
        $this->event('construct:before', [$string]);

        $this->string = $string;
    }

    public static function getEventAnchors(): PluggableAnchorsInterface
    {
        return (new PluggableAnchors())
            ->withAdded('construct:before')
            ->withAdded('setString:after');
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
