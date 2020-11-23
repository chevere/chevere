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

namespace Chevere\Components\Service\Traits;

use Chevere\Components\ClassMap\ClassMap;
use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\LogicException;

trait AssertDependenciesTrait
{
    public function getDependencies(): ClassMap
    {
        return new ClassMap;
    }

    final public function assertDependencies(): void
    {
        $dependencies = $this->getDependencies();
        $missing = [];
        /**
         * @var string $type
         * @var string $variable
         */
        foreach ($dependencies->toArray() as $type => $variable) {
            if (!isset($this->{$variable})) {
                $missing[] = $type . ' ' . $variable;
            }
        }
        if ($missing !== []) {
            throw new LogicException(
                (new Message('Missing dependencies %missing%'))
                    ->code('%missing%', implode(', ', $missing))
            );
        }
    }
}
