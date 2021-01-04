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

namespace Chevere\Interfaces\Dependent;

use Chevere\Interfaces\DataStructure\MappedInterface;
use Generator;

/**
 * Describes the component in charge of defining dependencies.
 */
interface DependenciesInterface extends MappedInterface
{
    public function withPut(string ...$namedDependencies): self;

    /**
     * @return Generator<string, string> Name to dependency class name
     */
    public function getGenerator(): Generator;
}
