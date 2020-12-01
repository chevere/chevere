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

namespace Chevere\Components\Plugin;

use Chevere\Components\Str\StrBool;
use RecursiveFilterIterator;
use RecursiveIterator;

/**
 * @codeCoverageIgnore
 */
final class PlugRecursiveFilterIterator extends RecursiveFilterIterator
{
    protected string $trailingName;

    public function __construct(
        RecursiveIterator $recursiveIterator,
        string $trailingName
    ) {
        $this->trailingName = $trailingName;
        parent::__construct($recursiveIterator);
    }

    public function accept(): bool
    {
        if ($this->hasChildren()) {
            return true;
        }

        return (new StrBool($this->current()->getFilename()))
            ->endsWith($this->trailingName);
    }

    public function getChildren(): RecursiveFilterIterator
    {
        /**
         * @var RecursiveFilterIterator $inner
         */
        $inner = $this->getInnerIterator();

        return new self($inner->getChildren(), $this->trailingName);
    }
}
