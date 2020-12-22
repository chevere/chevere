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

namespace Chevere\Tests\Pluggable\_resources\PlugsMapperTest;

use Chevere\Components\Pluggable\PluggableAnchors;
use Chevere\Components\Pluggable\Plugs\Hooks\Traits\PluggableHooksTrait;
use Chevere\Interfaces\Pluggable\PluggableAnchorsInterface;
use Chevere\Interfaces\Pluggable\Plugs\Hooks\PluggableHooksInterface;

class TestMappedHookable implements PluggableHooksInterface
{
    use PluggableHooksTrait;

    private string $string;

    public function __construct()
    {
        $string = '';
        $this->hook('hook-anchor-1', $string);

        $this->string = $string;
    }

    public static function getHookAnchors(): PluggableAnchorsInterface
    {
        return (new PluggableAnchors())
            ->withAdded('hook-anchor-1');
    }

    public function string(): string
    {
        return $this->string;
    }
}
