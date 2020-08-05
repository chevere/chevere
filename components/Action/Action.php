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

namespace Chevere\Components\Action;

use Chevere\Components\Parameter\Parameters;
use Chevere\Interfaces\Action\ActionInterface;
use Chevere\Interfaces\Parameter\ParametersInterface;
use Ds\Map;

abstract class Action implements ActionInterface
{
    private Map $arguments;

    private ParametersInterface $parameters;

    final public function __construct()
    {
        $this->parameters = $this->getParameters();
    }

    final public function parameters(): ParametersInterface
    {
        return $this->parameters;
    }

    public function getParameters(): ParametersInterface
    {
        return new Parameters;
    }
}
