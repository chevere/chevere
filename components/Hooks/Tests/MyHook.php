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

use Chevere\Components\Hooks\Interfaces\HookInterface;

class MyHook implements HookInterface
{
    public function anchor(): string
    {
        return 'setString:after';
    }

    public function className(): string
    {
        return MyHookable::class;
    }

    public function priority(): int
    {
        return 0;
    }

    public function __invoke(object $hookable): object
    {
        /**
         * @var MyHookable $hookable
         */
        $string = $hookable->string();
        $hookable->setString("(hooked $string)");

        return $hookable;
    }
}
