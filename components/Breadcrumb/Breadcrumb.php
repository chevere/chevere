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

namespace Chevere\Components\Breadcrumb;

use Chevere\Components\Breadcrumb\Exceptions\BreadcrumbException;
use Chevere\Components\Breadcrumb\Interfaces\BreadcrumbInterface;
use Chevere\Components\Message\Message;

/**
 * A general purpose iterator companion.
 */
final class Breadcrumb implements BreadcrumbInterface
{
    /** @var array [pos => $item] */
    private array $items = [];

    private int $pos = -1;

    private int $id = -1;

    public function has(int $pos): bool
    {
        return array_key_exists($pos, $this->items);
    }

    public function hasAny(): bool
    {
        return !empty($this->items);
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
        if (!array_key_exists($pos, $this->items)) {
            throw new BreadcrumbException(
                (new Message('Pos %pos% not found'))
                    ->code('%pos%', (string) $pos)
            );
        }
        $new = clone $this;
        unset($new->items[$pos]);

        return $new;
    }

    /**
     * Provides access to the breadcrumb as array.
     */
    public function toArray(): array
    {
        return $this->items;
    }

    /**
     * Provides access to the breadcrumb string.
     *
     * @return string items string `[item0][item1][itemN]...[itemN+1]`
     */
    public function toString(): string
    {
        if (0 == count($this->items)) {
            return '';
        }

        $return = '';
        foreach ($this->items as $item) {
            $return .= sprintf('[%s]', $item);
        }

        return $return;
    }
}
