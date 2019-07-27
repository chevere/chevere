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

/**
 * Provides filtering for the Api register process (directory scan).
 */
final class FilterIterator extends RecursiveFilterIterator
{
    /** @var array Accepted files array [GET.php, _GET.php, POST.php, ...] */
    private $acceptFilenames;

    /**
     * @param array  $methods      Accepted HTTP methods [GET,POST,etc.]
     * @param string $methodPrefix Method prefix used for root endpoint (no resource)
     *
     * @return self
     */
    public function generateAcceptedFilenames(array $methods, string $methodPrefix): self
    {
        foreach ($methods as $v) {
            $this->acceptFilenames[] = $v.'.php';
            $this->acceptFilenames[] = $methodPrefix.$v.'.php';
        }

        return $this;
    }

    /**
     * Overrides default getChildren to support the filter.
     */
    public function getChildren()
    {
        $children = parent::getChildren();
        $children->acceptFilenames = $this->acceptFilenames;

        return $children;
    }

    /**
     * The filter accept function.
     */
    public function accept(): bool
    {
        return $this->hasChildren() || in_array($this->current()->getFilename(), $this->acceptFilenames);
    }
}
