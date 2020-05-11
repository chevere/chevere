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

namespace Chevere\Components\Plugs\Tests\_resources\AssertPlugTest;

use Chevere\Components\Plugs\Interfaces\PlugInterface;

final class TestUnacceptedPlug implements PlugInterface
{
    public function for(): string
    {
        return '';
    }

    public function at(): string
    {
        return '';
    }

    public function priority(): int
    {
        return 0;
    }

    public function __invoke(&$argument): void
    {
        return;
    }
}
