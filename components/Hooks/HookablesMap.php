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

use Ds\Set;

final class HookablesMap
{
    private array $array = [];

    public function __construct()
    {
        $this->set = new Set;
    }

    public function withPut(string $hookable, string $filePath): HookablesMap
    {
        $new = clone $this;
        $new->array[$hookable] = $filePath;

        return $new;
    }

    public function has(string $hookable): bool
    {
        return isset($this->array[$hookable]);
    }

    public function get(string $hookable): string
    {
        return $this->array[$hookable];
    }

    /**
     * @return array [HookableClassname => HooksQueue@hooks.php, ]
     */
    public function toArray(): array
    {
        return $this->array;
    }
}
