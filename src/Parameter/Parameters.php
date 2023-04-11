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

namespace Chevere\Parameter;

use Chevere\DataStructure\Map;
use Chevere\DataStructure\Traits\MapTrait;
use Chevere\Parameter\Interfaces\ParameterInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;
use Chevere\Parameter\Traits\ParametersTrait;

final class Parameters implements ParametersInterface
{
    /**
     * @template-use MapTrait<ParameterInterface>
     */
    use MapTrait;

    use ParametersTrait;

    /**
     * @param ParameterInterface $parameter Required parameters
     */
    public function __construct(ParameterInterface ...$parameter)
    {
        $this->map = new Map();
        $this->requiredKeys = [];
        $this->optionalKeys = [];
        $this->addProperty('required', $parameter);
    }

    public function withAddedRequired(ParameterInterface ...$parameter): ParametersInterface
    {
        $new = clone $this;
        $new->addProperty('required', $parameter);

        return $new;
    }

    public function withAddedOptional(ParameterInterface ...$parameter): ParametersInterface
    {
        $new = clone $this;
        $new->addProperty('optional', $parameter);

        return $new;
    }

    public function without(string ...$name): ParametersInterface
    {
        $new = clone $this;
        $new->removeProperty(...$name);

        return $new;
    }
}
