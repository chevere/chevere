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

namespace Chevere\Components\Router\Interfaces\Properties;

use Chevere\Components\Common\Interfaces\ToStringInterface;

interface RegexPropertyInterface extends ToStringInterface
{
    /** @var string property name */
    const NAME = 'regex';

    /** @var string template pattern used for the regex property, %s gets replaced */
    const REGEX_TEPLATE = '#^(?%s)$#x';

    /** @var string regex pattern used to detect and capture routing elements */
    const REGEX_MATCHER = '~\#\^\(\?((\|(\S+) \(\*\:\d+\))+)\)\$\#x~';

    /** @var string %1 route, %2 id */
    const REGEX_ENTRY_TEMPLATE = '|%s (*:%s)';

    public function __construct(string $regex);
}
