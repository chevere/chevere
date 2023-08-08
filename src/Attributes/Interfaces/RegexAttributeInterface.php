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

namespace Chevere\Attributes\Interfaces;

use Chevere\Regex\Interfaces\RegexInterface;

/**
 * Describes the component in charge of defining a regex attribute.
 */
interface RegexAttributeInterface
{
    public function regex(): RegexInterface;
}
