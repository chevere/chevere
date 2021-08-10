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

namespace Chevere\Interfaces\Spec\Specs;

use Chevere\Interfaces\Filesystem\DirInterface;
use Chevere\Interfaces\Spec\SpecInterface;

/**
 * Describes the component in charge of defining the group spec.
 */
interface GroupSpecInterface extends SpecInterface
{
    public function __construct(DirInterface $specDir, string $group);

    /**
     * Return an instance with the specified `$routableSpec`.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$routableSpec`.
     */
    public function withAddedRoutableSpec(RouteSpecInterface $routableSpec): self;
}
