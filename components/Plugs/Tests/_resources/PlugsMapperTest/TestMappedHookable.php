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

namespace Chevere\Components\Plugs\Tests\_resources\PlugsMapperTest;

use Chevere\Components\Hooks\Interfaces\HookableInterface;
use Chevere\Components\Hooks\Traits\HookableTrait;
use Chevere\Components\Plugs\Interfaces\PlugableAnchorsInterface;
use Chevere\Components\Plugs\PlugableAnchors;

class TestMappedHookable implements HookableInterface
{
    use HookableTrait;

    private string $string;

    public static function getHookAnchors(): PlugableAnchorsInterface
    {
        return (new PlugableAnchors)
            ->withAddedAnchor('hook-anchor-1');
    }

    public function __construct()
    {
        $string = '';
        $this->hook('hook-anchor-1', $string);

        $this->string = $string;
    }

    public function string(): string
    {
        return $this->string;
    }
}
