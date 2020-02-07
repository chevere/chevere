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

namespace Chevere\Components\Api\Interfaces\src;

interface FilterIteratorInterface
{
    /**
     * @param array  $methods      Accepted HTTP methods [GET,POST,etc.]
     * @param string $methodPrefix Method prefix used for root endpoint (no resource)
     *
     * @return self
     */
    public function withAcceptFilenames(array $methods): FilterIteratorInterface;

    /**
     * Get accepte filenames
     *
     * @return array
     */
    public function acceptFilenames(): array;

    /**
     * Overrides default getChildren to support the filter.
     */
    public function getChildren();

    /**
     * The filter accept function.
     */
    public function accept(): bool;
}
