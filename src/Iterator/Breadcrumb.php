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

use Chevere\Iterator\Interfaces\BreadcrumbInterface;
use Chevere\Throwable\Exceptions\OutOfBoundsException;
use function Chevere\Message\message;

final class Breadcrumb implements BreadcrumbInterface
{
    /**
     * @var array<int, string>
     */
    private array $items = [];

    private int $pos = -1;

    private int $id = -1;

    public function __toString(): string
    {
        if ($this->items === []) {
            return '';
        }

        $return = '';
        foreach ($this->items as $item) {
            $return .= sprintf('[%s]', $item);
        }

        return $return;
    }

    public function has(int $pos): bool
    {
        return array_key_exists($pos, $this->items);
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function pos(): int
    {
        return $this->pos;
    }

    public function withAdded(string $item): BreadcrumbInterface
    {
        $new = clone $this;
        ++$new->id;
        $new->items[$new->id] = $item;
        $new->pos = $new->id;

        return $new;
    }

    public function withRemoved(int $pos): BreadcrumbInterface
    {
        if (! array_key_exists($pos, $this->items)) {
            throw new OutOfBoundsException(
                message('Pos %pos% not found')
                    ->withCode('%pos%', (string) $pos)
            );
        }
        $new = clone $this;
        unset($new->items[$pos]);

        return $new;
    }

    public function toArray(): array
    {
        return $this->items;
    }
}
