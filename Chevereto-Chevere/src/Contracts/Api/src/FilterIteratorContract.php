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

namespace Chevere\Contracts\Api\src;

interface FilterIteratorContract
{
    /**
     * @param array  $methods      Accepted HTTP methods [GET,POST,etc.]
     * @param string $methodPrefix Method prefix used for root endpoint (no resource)
     *
     * @return self
     */
    public function generateAcceptedFilenames(array $methods, string $methodPrefix): FilterIteratorContract;

    /**
     * Overrides default getChildren to support the filter.
     */
    public function getChildren();

    /**
     * The filter accept function.
     */
    public function accept(): bool;
}
