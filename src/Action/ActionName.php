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

namespace Chevere\Action;

use Chevere\Action\Interfaces\ActionInterface;
use Chevere\Action\Interfaces\ActionNameInterface;
use function Chevere\Common\assertClassName;

final class ActionName implements ActionNameInterface
{
    public function __construct(
        private string $name
    ) {
        assertClassName(ActionInterface::class, $this->name);
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
