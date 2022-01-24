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

namespace Chevere\Tests\Pluggable\Plug\Hook\_resources;

use Chevere\Pluggable\Interfaces\Plug\Hook\PluggableHooksInterface;
use Chevere\Pluggable\Interfaces\PluggableAnchorsInterface;
use Chevere\Pluggable\Plug\Hook\Traits\PluggableHooksTrait;
use Chevere\Pluggable\PluggableAnchors;

class TestHookable implements PluggableHooksInterface
{
    use PluggableHooksTrait;

    private string $string = '';

    public static function getHookAnchors(): PluggableAnchorsInterface
    {
        return new PluggableAnchors('setString:after');
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
