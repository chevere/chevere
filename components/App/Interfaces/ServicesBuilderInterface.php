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

interface ServicesBuilderInterface
{
    public function __construct(BuildInterface $build, ParametersInterface $parameters);

    /**
     * Provides access to the ServicesInterface instance generated.
     */
    public function services(): ServicesInterface;
}
