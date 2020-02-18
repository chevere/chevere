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

namespace Chevere\Components\Hooks;

use Chevere\Components\Message\Message;
use LogicException;

/**
 * Provides interaction for registered hooks.
 */
final class Hooks
{
    /** @var array ClassName, */
    private array $classMap;

    public function __construct(array $classMap)
    {
        $this->classMap = $classMap;
    }

    public function has(string $className): bool
    {
        return isset($this->classMap[$className]);
    }

    public function queue(string $className): Queue
    {
        $hooks_file = $this->classMap[$className] ?? null;
        $hooks = include $hooks_file ?? null;
        if ($hooks === null) {
            return new LogicException(
                (new Message('Unable to load hook for %locator%'))
                    ->code('%locator%', $locator)
                    ->toString()
            );
        }

        return new Queue($hooks);
    }
}
