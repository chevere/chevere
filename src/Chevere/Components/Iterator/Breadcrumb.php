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

namespace Chevere\Components\Iterator;

use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\OutOfRangeException;
use Chevere\Interfaces\Iterator\BreadcrumbInterface;

final class Breadcrumb implements BreadcrumbInterface
{
    /**
     * @var array [pos =>]
     */
    private array $items = [];

    private int $pos = -1;

    private int $id = -1;

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

    public function withAddedItem(string $item): BreadcrumbInterface
    {
        $new = clone $this;
        ++$new->id;
        $new->items[$new->id] = $item;
        $new->pos = $new->id;

        return $new;
    }

    public function withRemovedItem(int $pos): BreadcrumbInterface
    {
        if (! array_key_exists($pos, $this->items)) {
            throw new OutOfRangeException(
                (new Message('Pos %pos% not found'))
                    ->code('%pos%', (string) $pos)
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

    public function toString(): string
    {
        if (count($this->items) === 0) {
            return '';
        }

        $return = '';
        foreach ($this->items as $item) {
            $return .= sprintf('[%s]', $item);
        }

        return $return;
    }
}
