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

namespace Chevere\Interfaces\Serialize;

use Chevere\Interfaces\Type\TypeInterface;

/**
 * Describes the component in charge of handling `unserialize()`.
 */
interface DeserializeInterface
{
    /**
     * @throws LogicException
     */
    public function __construct(string $unserializable);

    /**
     * Provides access to the unserialize variable.
     */
    public function var();

    /**
     * Provides access to the TypeInterface instance for the unserialize variable.
     */
    public function type(): TypeInterface;
}
