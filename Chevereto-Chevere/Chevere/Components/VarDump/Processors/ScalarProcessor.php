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

use Chevere\Components\VarDump\Interfaces\ProcessorInterface;

/**
 * Handles integer, float (double), string
 */
final class ScalarProcessor extends AbstractProcessor
{
    public function withProcess(): ProcessorInterface
    {
        $new = clone $this;
        $new->val = '';
        $new->info = '';
        $is_string = is_string($this->varDump->var());
        $is_numeric = is_numeric($this->varDump->var());
        if ($is_string || $is_numeric) {
            $new->info = 'length=' .
                strlen(
                    $is_numeric
                        ? ((string) $this->varDump->var())
                        : $this->varDump->var()
                );
            $new->val = $this->varDump->formatter()->filterEncodedChars(strval($this->varDump->var()));
        }

        return $new;
    }
}
