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

namespace Chevere\Interfaces\Parameter;

use Chevere\Exceptions\Core\InvalidArgumentException;

/**
 * Describes the component in charge of defining a parameter of type object (typed).
 */
interface ObjectParameterInterface extends ParameterInterface
{
    /**
     * @throws InvalidArgumentException
     */
    public function __construct(string $className);
}
