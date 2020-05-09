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

namespace Chevere\Components\Hooks\Tests\_resources;

use Chevere\Components\Hooks\Interfaces\HookableInterface;
use Chevere\Components\Hooks\Traits\HookableTrait;
use Chevere\Components\Plugs\PlugableAnchors;

class TestHookable implements HookableInterface
{
    use HookableTrait;

    private string $string;

    public static function getHookAnchors(): PlugableAnchors
    {
        return (new PlugableAnchors)
            ->withAnchor('construct:before')
            ->withAnchor('setString:after');
    }

    public static function getEventAnchors(): PlugableAnchors
    {
        return (new PlugableAnchors)
            ->withAnchor('onSetString');
    }

    public function __construct()
    {
        $string = '';
        $this->hook('construct:before', $string);

        $this->string = $string;
    }

    public function setString(string $string): void
    {
        $this->string = $string;
        $this->hook('setString:after', $string);
        $this->string = $string;
    }

    public function string(): string
    {
        return $this->string;
    }
}
