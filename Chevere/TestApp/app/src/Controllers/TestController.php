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
use Chevere\Components\Hooks\Interfaces\HookableInterface;
use Chevere\Components\Hooks\Traits\HookableTrait;
use Chevere\Components\Plugs\PlugableAnchors;
use Chevere\Components\Regex\Regex;

class TestController extends Controller implements HookableInterface
{
    use HookableTrait;

    protected array $_data;

    public static function getHookAnchors(): PlugableAnchors
    {
        return (new PlugableAnchors)
            ->withAnchor('getParameters:after')
            ->withAnchor('run:before')
            ->withAnchor('run:after');
    }

    public function getParameters(): ControllerParametersInterface
    {
        $paremeters = (new ControllerParameters)
            ->withParameter(
                new ControllerParameter('name', new Regex('/^[\w]+$/'))
            )
            ->withParameter(
                new ControllerParameter('id', new Regex('/^[0-9]+$/'))
            );
        $this->hook('getParameters:after', $paremeters);

        return $paremeters;
    }

    public function run(ControllerArgumentsInterface $arguments): ControllerResponseInterface
    {
        $this->hook('run:before', $arguments);
        $response = new ControllerResponse(true);
        $data = [
            'userName' => $arguments->get('name'),
            'userId' => $arguments->get('id')
        ];
        $response = $response->withData($data);
        $this->hook('run:after', $response);

        return $response;
    }
}
