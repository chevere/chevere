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

namespace Chevere\VarDump\src;

/**
 * The template strings used by VarDump*.
 */
abstract class Template
{
    const HTML_INLINE_PREFIX = ' <span style="border-left: 1px solid rgba(236,240,241,.1);"></span>  ';
    const HTML_EMPHASIS = '<em>%s</em>';
}
