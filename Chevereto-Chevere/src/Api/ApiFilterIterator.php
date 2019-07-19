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

namespace Chevereto\Chevere\Api;

use RecursiveFilterIterator;

/**
 * Provides filtering for the Api register process (directory scan).
 */
class ApiFilterIterator extends RecursiveFilterIterator
{
    /** @var array The accepted files array [GET.php, _GET.php, POST.php, ...] */
    protected $acceptedFilenames;

    /**
     * @param array  $acceptedMethods Accepted HTTP methods [GET,POST,etc.]
     * @param string $methodPrefix    Method prefix used for root endpoint (no resource)
     *
     * @return self
     */
    public function generateAcceptedFilenames(array $acceptedMethods, string $methodPrefix): self
    {
        foreach ($acceptedMethods as $v) {
            $this->acceptedFilenames[] = $v.'.php';
            $this->acceptedFilenames[] = $methodPrefix.$v.'.php';
        }

        return $this;
    }

    /**
     * Overrides default getChildren to support the filter.
     */
    public function getChildren()
    {
        $children = parent::getChildren();
        $children->acceptedFilenames = $this->acceptedFilenames;

        return $children;
    }

    /**
     * The filter accept function.
     */
    public function accept(): bool
    {
        return $this->hasChildren() || in_array($this->current()->getFilename(), $this->acceptedFilenames);
    }
}
