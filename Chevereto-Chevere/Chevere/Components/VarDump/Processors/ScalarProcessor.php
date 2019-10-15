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

use Chevere\Components\VarDump\Processors\Traits\ProcessorTrait;
use Chevere\Components\VarDump\VarDump;
use Chevere\Contracts\VarDump\ProcessorContract;

/**
 * Handles integer, float (double), string
 */
final class ScalarProcessor implements ProcessorContract
{
    use ProcessorTrait;

    public function __construct($expression, VarDump $varDump)
    {
        $this->val = '';
        $this->parentheses = '';
        $is_string = is_string($expression);
        $is_numeric = is_numeric($expression);
        if ($is_string || $is_numeric) {
            $this->parentheses = 'length=' .
                strlen(
                    $is_numeric
                        ? ((string) $expression)
                        : $expression
                );
            $this->val = $varDump->formatter()->getEncodedChars(strval($expression));
        }
    }
}
