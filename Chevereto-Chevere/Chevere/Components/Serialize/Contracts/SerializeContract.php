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

namespace Chevere\Components\Serialize\Contracts;

use Chevere\Contracts\Variable\VariableExportContract;

interface SerializeContract
{
    /**
     * Creates a new instance.
     */
    public function __construct(VariableExportContract $variableExport);

    /**
     * Provides access to the serialized string.
     */
    public function toString();
}
