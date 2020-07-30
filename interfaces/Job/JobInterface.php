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

namespace Chevere\Interfaces\Job;

use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Core\UnexpectedValueException;
use Chevere\Interfaces\To\ToStringInterface;

/**
 * Describes the component in charge of defining a job unit name.
 */
interface JobInterface extends ToStringInterface
{
    const REGEX_KEY = '/^[\w-]*$/';

    /**
     * @throws UnexpectedValueException
     * @throws InvalidArgumentException
     */
    public function __construct(string $name);
}
