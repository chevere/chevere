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

namespace Chevere\Components\Hook;

use Chevere\Components\Path\Path;

/**
 * A container for the registered hooks.
 */
final class Container
{
    /** @var array */
    private $array;

    public function __construct()
    {
        $this->array = [] ?? include (new Path('var/hooks/registered.php'))->absolute();
    }

    public function getAnchor(object $that, string $anchor): array
    {
        return $this->array[get_class($that)][$anchor] ?? [];
    }
}
