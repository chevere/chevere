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

namespace Chevere\Parameter\Traits;

use Chevere\Parameter\Interfaces\ParametersInterface;

trait ParametersAccessTrait
{
    private ParametersInterface $parameters;

    public function parameters(): ParametersInterface
    {
        return $this->parameters;
    }
}
