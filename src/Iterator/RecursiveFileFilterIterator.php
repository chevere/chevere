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

use RecursiveFilterIterator;
use RecursiveIterator;

/**
 * @codeCoverageIgnore
 */
final class RecursiveFileFilterIterator extends RecursiveFilterIterator
{
    public function __construct(
        RecursiveIterator $recursiveIterator,
        protected string $trailingName
    ) {
        parent::__construct($recursiveIterator);
    }

    public function accept(): bool
    {
        if ($this->hasChildren()) {
            return true;
        }

        return str_ends_with(
            $this->current()->getFilename(),
            $this->trailingName
        );
    }

    public function getChildren(): RecursiveFilterIterator
    {
        /** @var RecursiveFilterIterator $inner */
        $inner = $this->getInnerIterator();

        return new self($inner->getChildren(), $this->trailingName);
    }
}
