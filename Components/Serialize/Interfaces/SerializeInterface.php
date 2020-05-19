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

namespace Chevere\Components\Serialize\Interfaces;

use Chevere\Components\VarExportable\Interfaces\VarExportableInterface;

interface SerializeInterface
{
    public function __construct(VarExportableInterface $varExportable);

    /**
     * Provides access to the serialized string.
     */
    public function toString();
}
