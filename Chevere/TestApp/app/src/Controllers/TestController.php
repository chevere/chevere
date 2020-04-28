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
use Chevere\Components\Controller\ControllerArguments;
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

    protected ControllerArguments $_arguments;

    protected array $_data;

    public static function getHookAnchors(): HookAnchors
    {
        return (new HookAnchors)
            ->withPut('getParameters:after')
            ->withPut('run:before')
            ->withPut('run:after');
    }

    public function getParameters(): ControllerParametersInterface
    {
        $this->_paremeters = (new ControllerParameters)
            ->with(
                new ControllerParameter('name', new Regex('/^[\w]+$/'))
            )
            ->with(
                new ControllerParameter('id', new Regex('/^[0-9]+$/'))
            );
        $this->hook('getParameters:after');

        return $this->_paremeters;
    }

    public function run(ControllerArgumentsInterface $arguments): ControllerResponseInterface
    {
        $this->_arguments = $arguments;
        $this->hook('run:before');
        $this->_response = new ControllerResponse(true);
        $this->_data = [
            'userName' => $this->_arguments->get('name'),
            'userId' => $this->_arguments->get('id')
        ];
        $this->_response = $this->_response->withData($this->_data);
        $this->hook('run:after');

        return $this->_response;
    }
}
