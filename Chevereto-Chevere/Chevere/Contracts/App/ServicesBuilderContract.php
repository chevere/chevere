<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Contracts\App;

interface ServicesBuilderContract
{
    /**
     * Creates a new instance.
     *
     * @param BuildContract      $build      The build containg AppContract services (if any)
     * @param ParametersContract $parameters The application parameters which alter this services builder
     */
    public function __construct(BuildContract $build, ParametersContract $parameters);

    /**
     * Provides access to the ServicesContract instance generated.
     */
    public function services(): ServicesContract;
}
