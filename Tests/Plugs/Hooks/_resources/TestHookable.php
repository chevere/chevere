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

namespace Chevere\Tests\Plugs\Hooks\_resources;

use Chevere\Interfaces\Plugin\PluggableAnchorsInterface;
use Chevere\Components\Plugin\PluggableAnchors;
use Chevere\Interfaces\Plugs\Hooks\PluggableHooksInterface;
use Chevere\Components\Plugs\Hooks\Traits\PluggableHooksTrait;

class TestHookable implements PluggableHooksInterface
{
    use PluggableHooksTrait;

    private string $string;

    public static function getHookAnchors(): PluggableAnchorsInterface
    {
        return (new PluggableAnchors)
            ->withAddedAnchor('construct:before')
            ->withAddedAnchor('setString:after');
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
