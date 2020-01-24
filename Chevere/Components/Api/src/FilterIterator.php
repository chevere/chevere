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

namespace Chevere\Components\Api\src;

use RecursiveFilterIterator;
use Chevere\Components\Api\Interfaces\src\FilterIteratorInterface;

/**
 * Provides filtering for the Api register process (directory scan).
 */
final class FilterIterator extends RecursiveFilterIterator implements FilterIteratorInterface
{
    /** @var array Accepted files array [GET.php, _GET.php, POST.php, ...] */
    private array $acceptFilenames;

    /**
     * {@inheritdoc}
     */
    public function withAcceptFilenames(array $methods): FilterIteratorInterface
    {
        $new = clone $this;
        foreach ($methods as $v) {
            $new->acceptFilenames[] = $v . '.php';
        }

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function acceptFilenames(): array
    {
        return $this->acceptFilenames;
    }

    /**
     * {@inheritdoc}
     */
    public function getChildren(): RecursiveFilterIterator
    {
        $children = parent::getChildren();
        $children->acceptFilenames = $this->acceptFilenames;

        return $children;
    }

    /**
     * {@inheritdoc}
     */
    public function accept(): bool
    {
        return $this->hasChildren() || in_array($this->current()->getFilename(), $this->acceptFilenames);
    }
}
