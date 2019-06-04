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
 * Stores the template strings used by VarDumper.
 */
class Template
{
    const HTML_INLINE_PREFIX = ' <span style="border-left: 1px solid #bdc3c7;"></span>  ';
    const HTML_EMPHASIS = '<em>%s</em>';
}
