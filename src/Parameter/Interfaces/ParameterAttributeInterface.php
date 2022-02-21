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

namespace Chevere\Parameter\Interfaces;

use Chevere\Regex\Interfaces\RegexInterface;

/**
 * Describes the component in charge of defining the base parameter attribute.
 */
interface ParameterAttributeInterface
{
    public function description(): string;

    public function regex(): RegexInterface;
}
