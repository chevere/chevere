<?php

declare(strict_types=1);

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevere\Api\src;

use RecursiveFilterIterator;
use Chevere\Contracts\Api\src\FilterIteratorContract;

/**
 * Provides filtering for the Api register process (directory scan).
 */
final class FilterIterator extends RecursiveFilterIterator implements FilterIteratorContract
{
    /** @var array Accepted files array [GET.php, _GET.php, POST.php, ...] */
    private $acceptFilenames;

    /**
     * {@inheritdoc}
     */
    public function withAcceptFilenames(array $methods, string $methodPrefix): FilterIteratorContract
    {
        $new = clone $this;
        foreach ($methods as $v) {
            $new->acceptFilenames[] = $v . '.php';
            $new->acceptFilenames[] = $methodPrefix . $v . '.php';
        }

        return $new;
    }

    public function acceptFilenames(): array
    {
        return $this->acceptFilenames;
    }

    /**
     * {@inheritdoc}
     */
    public function getChildren()
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
