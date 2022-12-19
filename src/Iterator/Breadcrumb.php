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

namespace Chevere\Iterator;

use Chevere\DataStructure\Traits\VectorTrait;
use Chevere\DataStructure\Vector;
use function Chevere\DataStructure\vectorToArray;
use Chevere\Iterator\Interfaces\BreadcrumbInterface;

final class Breadcrumb implements BreadcrumbInterface
{
    /**
     * @template-use VectorTrait<string>
     */
    use VectorTrait;

    public function __construct()
    {
        $this->vector = new Vector();
    }

    public function __toString(): string
    {
        if (count($this->vector) === 0) {
            return '';
        }

        $return = '';
        foreach ($this->vector->getIterator() as $item) {
            $return .= sprintf('[%s]', $item);
        }

        return $return;
    }

    public function has(int $pos): bool
    {
        return $this->vector->has($pos);
    }

    public function count(): int
    {
        return count($this->vector);
    }

    public function pos(): int
    {
        return $this->vector->count() - 1;
    }

    public function withAdded(string $item): BreadcrumbInterface
    {
        $new = clone $this;
        $new->vector = $new->vector->withPush($item);

        return $new;
    }

    public function withRemoved(int $pos): BreadcrumbInterface
    {
        $new = clone $this;
        $new->vector = $new->vector->withRemove($pos);

        return $new;
    }

    public function toArray(): array
    {
        return vectorToArray($this->vector);
    }
}
