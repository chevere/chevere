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

namespace Chevere\Common;

final class Symbol
{
    /**
     * Fully qualified name of a class or function
     */
    public const FQN = '#^([\w\\\d]+)$#';

    /**
     * Class::constant
     */
    public const CLASS_CONSTANT = '#^([\w\\\d]+)::([\w\\\d]+)$#';

    /**
     * Class::method()
     */
    public const CLASS_METHOD = '#^([\w\\\d]+)::([\w\\\d]+)\(\)$#';

    /**
     * Class::method($parameter)
     */
    public const CLASS_METHOD_PARAMETER = '#^([\w\\\d]+)::([\w\\\d]+)\(\$([\w\\\d]+)\)$#';

    /**
     * Class::$property
     */
    public const CLASS_PROPERTY = '#^([\w\\\d]+)::\$([\w\\\d]+)$#';

    /**
     * Function($parameter)
     */
    public const FUNCTION_PARAMETER = '#^([\w\\\d]+)\(\$([\w\\\d]+)\)$#';
}
