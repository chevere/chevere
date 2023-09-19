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

namespace Chevere\Action\Interfaces;

use Chevere\Parameter\Interfaces\CastArgumentInterface;
use Chevere\Parameter\Interfaces\ParameterInterface;

/**
 * Describes the component in charge of defining a single logic action.
 * @method mixed run() Defines the action run logic.
 */
interface ActionInterface
{
    /**
     * Defines expected response parameter when executing `run` method.
     */
    public static function acceptResponse(): ParameterInterface;

    /**
     * Retrieves `run` response checked against `acceptResponse`.
     */
    public function getResponse(mixed ...$argument): CastArgumentInterface;

    /**
     * Assert for static context.
     */
    public static function assert(): void;
}
