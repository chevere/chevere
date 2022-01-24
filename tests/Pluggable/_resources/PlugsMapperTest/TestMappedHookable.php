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

use Chevere\Pluggable\Interfaces\Plug\Hook\PluggableHooksInterface;
use Chevere\Pluggable\Interfaces\PluggableAnchorsInterface;
use Chevere\Pluggable\Plug\Hook\Traits\PluggableHooksTrait;
use Chevere\Pluggable\PluggableAnchors;

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
        return new PluggableAnchors('hook-anchor-1');
    }

    public function string(): string
    {
        return $this->string;
    }
}
