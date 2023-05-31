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
use Chevere\DataStructure\Vector;
use Chevere\Parameter\Interfaces\ParameterInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;
use Chevere\Parameter\Traits\ParametersGetTypedTrait;
use Chevere\Parameter\Traits\ParametersTrait;

final class Parameters implements ParametersInterface
{
    /**
     * @template-use MapTrait<ParameterInterface>
     */
    use MapTrait;

    use ParametersTrait;
    use ParametersGetTypedTrait;

    /**
     * @param ParameterInterface $parameter Required parameters
     */
    public function __construct(ParameterInterface ...$parameter)
    {
        $this->map = new Map();
        $this->required = new Vector();
        $this->optional = new Vector();
        foreach ($parameter as $name => $item) {
            $name = strval($name);
            $this->addProperty('required', $name, $item);
        }
    }

    public function withAddedRequired(string $name, ParameterInterface $parameter): ParametersInterface
    {
        $new = clone $this;
        $new->addProperty('required', $name, $parameter);

        return $new;
    }

    public function withAddedOptional(string $name, ParameterInterface $parameter): ParametersInterface
    {
        $new = clone $this;
        $new->addProperty('optional', $name, $parameter);

        return $new;
    }

    public function without(string ...$name): ParametersInterface
    {
        $new = clone $this;
        $new->map = $new->map->without(...$name);
        $requiredDiff = array_diff($new->required->toArray(), $name);
        $optionalDiff = array_diff($new->optional->toArray(), $name);
        $new->required = new Vector(...$requiredDiff);
        $new->optional = new Vector(...$optionalDiff);

        return $new;
    }
}
