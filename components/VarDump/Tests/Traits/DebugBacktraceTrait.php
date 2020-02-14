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

namespace Chevere\Components\VarDump\Tests\Traits;

trait DebugBacktraceTrait
{
    private function getDebugBacktrace(): array
    {
        return [
            0 => [
                'file' => 'file@handler',
                'line' => 101,
                'function' => 'function@handler',
                'class' => 'class@handler',
                'type' => '->',
                'args' => []
            ],
            1 => [
                'file' => __FILE__,
                'line' => __LINE__,
                'function' => __FUNCTION__,
                'class' => __CLASS__,
                'type' => '->',
                'args' => []
            ]
        ];
    }
}
