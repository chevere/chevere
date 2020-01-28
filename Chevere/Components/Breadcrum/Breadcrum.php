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

namespace Chevere\Components\Breadcrum;

use Chevere\Components\Breadcrum\Exceptions\BreadcrumException;
use Chevere\Components\Message\Message;
use Chevere\Components\Breadcrum\Interfaces\BreadcrumInterface;

/**
 * A general purpose iterator companion.
 */
final class Breadcrum implements BreadcrumInterface
{
    /** @vvar arrau [pos => $item] */
    private array $items = [];

    private int $pos = -1;

    private int $id = -1;

    /**
     * {@inheritdoc}
     */
    public function has(int $pos): bool
    {
        return array_key_exists($pos, $this->items);
    }

    /**
     * {@inheritdoc}
     */
    public function hasAny(): bool
    {
        return !empty($this->items);
    }

    /**
     * {@inheritdoc}
     */
    public function pos(): int
    {
        return $this->pos;
    }

    /**
     * {@inheritdoc}
     */
    public function withAddedItem(string $item): BreadcrumInterface
    {
        $new = clone $this;
        ++$new->id;
        $new->items[$new->id] = $item;
        $new->pos = $new->id;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function withRemovedItem(int $pos): BreadcrumInterface
    {
        if (!array_key_exists($pos, $this->items)) {
            throw new BreadcrumException(
                (new Message('Pos %pos% not found'))
                    ->code('%pos%', (string) $pos)
                    ->toString()
            );
        }
        $new = clone $this;
        unset($new->items[$pos]);

        return $new;
    }

    /**
     * {@inheritdoc}
     *
     * Provides access to the breadcrum as array.
     */
    public function toArray(): array
    {
        return $this->items;
    }

    /**
     * {@inheritdoc}
     *
     * Provides access to the breadcrum string.
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
