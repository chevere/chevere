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
    private $var;

    public function __construct($var)
    {
        $this->var = $var;
    }

    public function withProcess(): ProcessorInterface
    {
        $new = clone $this;
        $is_string = is_string($this->var);
        $is_numeric = is_numeric($this->var);
        if ($is_string || $is_numeric) {
            $new->info = 'length=' .
                strlen(
                    $is_numeric
                        ? ((string) $this->var)
                        : $this->var
                );
            $new->val = $this->formatter->filterEncodedChars(strval($this->var));
        }

        return $new;
    }
}
