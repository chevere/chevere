<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Contracts\Path;

interface PathAppContract extends RelativePathContract
{
    /**
     * Construct a new instance.
     */
    public function __construct(string $path);
}
