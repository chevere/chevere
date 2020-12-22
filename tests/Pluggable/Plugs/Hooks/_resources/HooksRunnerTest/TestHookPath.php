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

namespace Chevere\Tests\Pluggable\Plugs\Hooks\_resources\HooksRunnerTest;

use Chevere\Interfaces\Filesystem\PathInterface;
use Chevere\Interfaces\Pluggable\Plugs\Hooks\HookInterface;

class TestHookPath implements HookInterface
{
    /**
     * @param PathInterface $argument
     */
    public function __invoke(&$argument): void
    {
        $argument = $argument->getChild('hooked/');
    }

    public function anchor(): string
    {
        return 'path';
    }

    public function at(): string
    {
        return TestHookable::class;
    }

    public function priority(): int
    {
        return 0;
    }
}
