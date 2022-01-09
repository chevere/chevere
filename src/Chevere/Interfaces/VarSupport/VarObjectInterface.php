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

namespace Chevere\Interfaces\VarSupport;

use Chevere\Exceptions\VarSupport\VarObjectNotClonableException;

/**
 * Describes the component in charge of defining a object variable.
 */
interface VarObjectInterface
{
    public function __construct(object $var);

    /**
     * @throws VarObjectNotClonableException
     */
    public function assertClonable(): void;
}
