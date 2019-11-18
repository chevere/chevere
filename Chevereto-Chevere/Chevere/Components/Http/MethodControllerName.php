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

namespace Chevere\Components\Http;

use Chevere\Contracts\Controller\ControllerNameContract;
use Chevere\Contracts\Http\MethodContract;
use Chevere\Contracts\Http\MethodControllerNameContract;

final class MethodControllerName implements MethodControllerNameContract
{
    /** @var MethodContract */
    private $method;

    /** @var ControllerNameContract */
    private $controllerName;

    /**
     * {@inheritdoc}
     */
    public function __construct(MethodContract $method, ControllerNameContract $controllerName)
    {
        $this->method = $method;
        $this->controllerName = $controllerName;
    }

    /**
     * {@inheritdoc}
     */
    public function method(): MethodContract
    {
        return $this->method;
    }

    /**
     * {@inheritdoc}
     */
    public function controllerName(): ControllerNameContract
    {
        return $this->controllerName;
    }
}
