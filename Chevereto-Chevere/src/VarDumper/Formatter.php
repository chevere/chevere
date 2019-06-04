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

namespace Chevereto\Chevere\VarDumper;

/**
 * Formats the VarDumper analyzed data and provides templating tools.
 */
class Formatter
{
    public function __construct(VarDumper $varDumper)
    {
    }

    public static function getEmphasized(string $string): string
    {
        return sprintf(Template::HTML_EMPHASIS, $string);
    }
}
