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

namespace Chevere\Interfaces\Workflow;

use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Interfaces\To\ToStringInterface;

/**
 * Describes the component in charge of defining a step unit name.
 */
interface StepInterface extends ToStringInterface
{
    const REGEX_KEY = '/^[\w-]*$/';

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(string $name);
}
