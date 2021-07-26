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

use Chevere\Interfaces\Common\AttributesInterface;
use Chevere\Interfaces\Common\DescriptionInterface;
use Chevere\Interfaces\Type\TypeInterface;

/**
 * Describes the component in charge of defining a parameter.
 */
interface ParameterInterface extends DescriptionInterface, AttributesInterface
{
    public function __construct(
        string $description = ''
    );

    /**
     * Provides access to the type instance.
     */
    public function type(): TypeInterface;

    /**W
     * Return an instance with the specified `$description`.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$description`.
     */
    public function withDescription(string $description): static;
}
