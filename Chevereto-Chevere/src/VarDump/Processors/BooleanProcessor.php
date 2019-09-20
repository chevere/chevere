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

namespace Chevere\VarDump\Processors;

use Chevere\Contracts\VarDump\ProcessorContract;
use Chevere\VarDump\Processors\Traits\ProcessorTrait;
use Chevere\VarDump\VarDump;

final class BooleanProcessor implements ProcessorContract
{
    use ProcessorTrait;

    public function __construct(bool $expression, VarDump $varDump)
    {
        $this->val = $expression ? 'TRUE' : 'FALSE';
        $this->parentheses = '';
    }
}
