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

namespace Chevereto\Core;

abstract class FromString
{
    /** @var string Description of the string used to create object from string */
    protected static $stringDescription = 'No description';

    /** @var string Regex used when creating object from string */
    protected static $stringRegex = '.*';
}
