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

namespace Chevere\Interfaces\Controller\Attributes;

/**
 * Describes the component in charge of defining controller relation attribute.
 */
interface RelationInterface
{
    public function __construct(string $relation);

    public function relation(): string;
}