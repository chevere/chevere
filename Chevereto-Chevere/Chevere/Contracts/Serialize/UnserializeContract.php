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

namespace Chevere\Contracts\Serialize;

use Chevere\Contracts\Type\TypeContract;
use Chevere\Components\Folder\Exceptions\UnserializeException;

interface UnserializeContract
{
    /**
     * Creates a new instance.
     *
     * @throws UnserializeException if $serialized can't be unserialized
     */
    public function __construct(string $serialized);

    /**
     * Provides access to the unserialized variable.
     */
    public function var();

    /**
     * Provides access to the TypeContract instance for the unserialized variable.
     */
    public function type(): TypeContract;
}
