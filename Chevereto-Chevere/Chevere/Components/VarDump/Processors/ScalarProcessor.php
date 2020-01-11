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

use Chevere\Components\VarDump\Contracts\VarDumpContract;

/**
 * Handles integer, float (double), string
 */
final class ScalarProcessor extends AbstractProcessor
{
    public function __construct(VarDumpContract $varDump)
    {
        $this->val = '';
        $this->info = '';
        $is_string = is_string($varDump->var());
        $is_numeric = is_numeric($varDump->var());
        if ($is_string || $is_numeric) {
            $this->info = 'length=' .
                strlen(
                    $is_numeric
                        ? ((string) $varDump->var())
                        : $varDump->var()
                );
            $this->val = $varDump->formatter()->filterEncodedChars(strval($varDump->var()));
        }
    }
}
