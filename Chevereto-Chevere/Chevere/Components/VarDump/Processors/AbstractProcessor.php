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

namespace Chevere\Components\VarDump\Processors;

abstract class AbstractProcessor
{
    /** @var string */
    protected string $info;

    /** @var string */
    protected string $val;

    final public function info(): string
    {
        return $this->info;
    }

    final public function val(): string
    {
        return $this->val;
    }
}
