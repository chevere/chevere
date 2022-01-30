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

namespace Chevere\Common\Interfaces;

use Ds\Map;

/**
 * Describes the component in charge of providing attributes.
 */
interface AttributesInterface
{
    public function withAddedAttribute(string ...$attributes): static;

    public function withoutAttribute(string ...$attributes): static;

    public function hasAttribute(string ...$attributes): bool;

    public function attributes(): Map;
}
