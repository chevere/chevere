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

namespace Chevere\Components\Hooks\Tests;

use Chevere\Components\Hooks\HookAnchors;
use Chevere\Components\Hooks\Interfaces\HookableInterface;
use Chevere\Components\Hooks\Traits\HookableTrait;

class MyHookable implements HookableInterface
{
    use HookableTrait;

    private string $string = '';

    public static function getHookAnchors(): HookAnchors
    {
        return (new HookAnchors)
            ->withPut('construct:before')
            ->withPut('setString:after');
    }

    public function __construct()
    {
        $this->hook('construct:before');
    }

    public function setString(string $string): void
    {
        $this->string = $string;
        $this->hook('setString:after');
    }

    public function string(): string
    {
        return $this->string;
    }
}

/**
 * Same as MyHookable but no hooks are registered for this class.
 */
final class MyHookableWithoutHooks extends MyHookable
{
}

/**
 * Same as MyHookable but the hookable isn't registered.
 */
final class MyHookableWithNotRegisteredClass extends MyHookable
{
}

/**
 * Same as MyHookable but hooks file is missing.
 */
final class MyHookableWithMissingHooks extends MyHookable
{
}

/**
 * Same as MyHookable but hooks file is corrupted.
 */
final class MyHookableWithCorruptedHooks extends MyHookable
{
}
