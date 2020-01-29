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

namespace Chevere\Components\App\Interfaces;

interface ResolvableInterface
{
    public function __construct(BuilderInterface $builder);

    /**
     * @return BuilderInterface A resolvable BuilderInterface
     */
    public function builder(): BuilderInterface;
}
