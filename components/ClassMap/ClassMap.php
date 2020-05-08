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

namespace Chevere\Components\ClassMap;

use Chevere\Components\ExceptionHandler\Exceptions\Exception;
use Chevere\Components\Message\Message;

final class ClassMap
{
    /** @var array [className => string] */
    private array $classMap = [];

    /** @var array [string => className] */
    private array $flip = [];

    public function withPut(string $className, string $string): ClassMap
    {
        $known = $this->flip[$string] ?? null;
        if ($known && $known !== $className) {
            throw new Exception(
                (new Message('Attemping to map %className% to the same mapping of %known% -> %string%'))
                    ->code('%className%', $className)
                    ->code('%known%', $known)
                    ->code('%string%', $string)
            );
        }
        $new = clone $this;
        $new->classMap[$className] = $string;

        return $new;
    }

    public function has(string $className): bool
    {
        return isset($this->classMap[$className]);
    }

    public function get(string $className): string
    {
        return $this->classMap[$className];
    }

    /**
     * @return array [classname => string, ]
     */
    public function toArray(): array
    {
        return $this->classMap;
    }
}
