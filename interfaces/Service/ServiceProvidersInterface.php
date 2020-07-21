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

namespace Chevere\Interfaces\Service;

use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Core\OverflowException;
use Chevere\Interfaces\DataStructures\DsMapInterface;
use Generator;

/**
 * Describes the component in charge of collecting service providers in a serviceable interface.
 */
interface ServiceProvidersInterface extends DsMapInterface
{
    public function __construct(ServiceableInterface $serviceable);

    /**
     * Return an instance with the specified added `$method`.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified added `$method`.
     *
     * The `$method` argument must be a public method in `$serviceable` taking
     * one single `$argument` for the concrete service class implementing `ServiceInterface`.
     *
     * @throws InvalidArgumentException If `$method` doesn't exists in `$serviceable`.
     * @throws LogicException If `$method` visibility is not public.
     * @throws ArgumentCountException If `$method` doesn't define its single argument.
     * @throws UnexpectedValueException If `$method` single argument is not typed against a class implementing `ServiceInterface`.
     * @throws OverflowException If `$method` has been already added.
     */
    public function withAdded(string $method): ServiceProvidersInterface;

    /**
     * ```php
     * yield $method => $serviceName
     * ```
     * @return Generator<string, string>
     */
    public function getGenerator(): Generator;
}
