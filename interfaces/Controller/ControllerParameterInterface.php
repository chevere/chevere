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

namespace Chevere\Interfaces\Controller;

use Chevere\Exceptions\Controller\ControllerParameterNameInvalidException;
use Chevere\Interfaces\Regex\RegexInterface;

/**
 * Describes the component in charge of handling controller parameters.
 */
interface ControllerParameterInterface
{
    /**
     * @throws ControllerParameterNameInvalidException
     */
    public function __construct(string $name, RegexInterface $regex);

    /**
     * Indicates whether the parameter is required.
     */
    public function isRequired(): bool;

    /**
     * Provides access to the parameter name.
     */
    public function name(): string;

    /**
     * Provides access to the parameter regex instance.
     */
    public function regex(): RegexInterface;

    /**
     * Provides access to the name instance.
     */
    public function description(): string;

    /**
     * Return an instance with the specified description.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified description.
     */
    public function withDescription(string $string): ControllerParameterInterface;

    /**
     * Return an instance with the specified required flag.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified required flag.
     */
    public function withIsRequired(bool $bool): ControllerParameterInterface;
}
