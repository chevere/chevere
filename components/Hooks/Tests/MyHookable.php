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

use Chevere\Components\Hooks\Interfaces\HookableInterface;
use Chevere\Components\Hooks\Traits\HookableTrait;

final class MyHookable implements HookableInterface
{
    const HOOK_SET_STRING = 'setString';

    use HookableTrait;

    private string $string = '';

    public function __construct()
    {
        $this->setHooksQueue(true);
    }

    public function anchors(): array
    {
        return [
            self::HOOK_SET_STRING
        ];
    }

    public function setString(string $string): void
    {
        $this->string = $string;
        $this->hook(self::HOOK_SET_STRING);
    }

    public function string(): string
    {
        return $this->string;
    }
}
