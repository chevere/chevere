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
    public function __construct(string $description = '');

    /**
     * This method runs before the `__construct` method.
     */
    public function setUp(): void;

    /**
     * Provides access to the type instance.
     */
    public function type(): TypeInterface;

    /**
     * Gets a new parameter type instance.
     */
    public function getType(): TypeInterface;
}
