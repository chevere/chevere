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

namespace Chevere\Components\Router\Interfaces;

use Chevere\Components\Regex\Interfaces\RegexInterface;

interface RouterRegexInterface
{
    /** @var string template pattern used for the regex property, %s gets replaced */
    const TEMPLATE = '#^(?%s)$#x';

    /** @var string regex pattern used to detect and capture routing elements */
    const MATCHER = '~\#\^\(\?((\|(\S+) \(\*\:\d+\))+)\)\$\#x~';

    /** @var string %1 route, %2 id */
    const TEMPLATE_ENTRY = '|%s (*:%s)';

    public function regex(): RegexInterface;
}
