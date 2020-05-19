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

namespace Chevere\Components\Runtime;

use Chevere\Components\Runtime\Interfaces\RuntimeInterface;
use Chevere\Components\Runtime\Interfaces\SetInterface;
use Ds\Map;
use function DeepCopy\deep_copy;

/**
 * Runtime applies runtime config and provide data about the App Runtime.
 */
final class Runtime implements RuntimeInterface
{
    private Map $data;

    public function __construct()
    {
        $this->data = new Map;
    }

    public function withSet(SetInterface $set): RuntimeInterface
    {
        $new = clone $this;
        $new->data->put($set->name(), $set->value());

        return $new;
    }

    public function data(): Map
    {
        return deep_copy($this->data);
    }
}
