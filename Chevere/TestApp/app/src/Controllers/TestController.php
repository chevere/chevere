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

namespace Chevere\TestApp\App\Controllers;

use Chevere\Components\Controller\Controller;
use Chevere\Components\Controller\ControllerParameter;
use Chevere\Components\Controller\ControllerParameters;
use Chevere\Components\Controller\ControllerResponse;
use Chevere\Components\Controller\Interfaces\ControllerArgumentsInterface;
use Chevere\Components\Controller\Interfaces\ControllerParametersInterface;
use Chevere\Components\Controller\Interfaces\ControllerResponseInterface;
use Chevere\Components\Hooks\HookAnchors;
use Chevere\Components\Hooks\Interfaces\HookableInterface;
use Chevere\Components\Hooks\Traits\HookableTrait;
use Chevere\Components\Regex\Regex;

class TestController extends Controller implements HookableInterface
{
    use HookableTrait;

    protected ControllerParameters $_parameters;

    protected ControllerResponse $_response;

    public static function anchors(): HookAnchors
    {
        return (new HookAnchors)
            ->withPut('getParameters:after')
            ->withPut('run:before')
            ->withPut('run:after');
    }

    public function getParameters(): ControllerParametersInterface
    {
        $this->_paremeters = (new ControllerParameters)
            ->withPut(new ControllerParameter(
                'name',
                new Regex('/^[\w]+$/')
            ))
            ->withPut(new ControllerParameter(
                'id',
                new Regex('/^[0-9]+$/')
            ));
        $this->hook('getParameters:after');

        return $this->_paremeters;
    }

    public function run(ControllerArgumentsInterface $arguments): ControllerResponseInterface
    {
        $this->hook('run:before');
        $this->_response = (new ControllerResponse(true))
            ->withData([
                'userName' => $arguments->get('name'),
                'userId' => $arguments->get('id')
            ]);
        $this->hook('run:after');

        return $this->_response;
    }
}
