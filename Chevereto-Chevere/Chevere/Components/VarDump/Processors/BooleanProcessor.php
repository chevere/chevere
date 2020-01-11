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

use Chevere\Components\VarDump\Contracts\ProcessorContract;
use Chevere\Components\VarDump\Contracts\VarDumpContract;

final class BooleanProcessor extends AbstractProcessor
{
    private bool $var;

    public function withProcess(): ProcessorContract
    {
        $new = clone $this;
        $new->var = $new->varDump->var();
        $new->val = $new->var ? 'true' : 'false';
        $new->info = '';

        return $new;
    }
}
