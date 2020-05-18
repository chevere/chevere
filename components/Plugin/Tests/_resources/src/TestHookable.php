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

namespace Chevere\Components\Plugin\Tests\_resources\src;

use Chevere\Components\Plugs\Hooks\Interfaces\PluggableHooksInterface;
use Chevere\Components\Plugs\Hooks\Traits\PluggableHookTrait;
use Chevere\Components\Plugin\Interfaces\PluggableAnchorsInterface;
use Chevere\Components\Plugin\PluggableAnchors;

class TestHookable implements PluggableHooksInterface
{
    use PluggableHookTrait;

    private string $string;

    public static function getHookAnchors(): PluggableAnchorsInterface
    {
        return (new PluggableAnchors)
            ->withAddedAnchor('hook-anchor-1')
            ->withAddedAnchor('hook-anchor-2');
    }

    public function __construct()
    {
        $string = '';
        $this->hook('hook-anchor-1', $string);

        $this->string = $string;
    }

    public function setString(string $string): void
    {
        $this->string = $string;
        $this->hook('hook-anchor-2', $string);
        $this->string = $string;
    }

    public function string(): string
    {
        return $this->string;
    }
}
