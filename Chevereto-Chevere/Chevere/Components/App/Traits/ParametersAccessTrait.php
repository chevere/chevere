<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\App\Traits;

use Chevere\Contracts\App\ParametersContract;

trait ParametersAccessTrait
{
    /** @var ParametersContract */
    private $parameters;

    public function hasParameters(): bool
    {
        return isset($this->parameters);
    }

    public function parameters(): ParametersContract
    {
        return $this->parameters;
    }
}
