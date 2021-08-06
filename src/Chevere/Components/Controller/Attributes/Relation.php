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

namespace Chevere\Components\Controller\Attributes;

use Attribute;
use Chevere\Interfaces\Controller\Attributes\RelationInterface;

#[Attribute]
class Relation implements RelationInterface
{
    public function __construct(protected string $relation)
    {
    }

    public function relation(): string
    {
        return $this->relation;
    }
}
