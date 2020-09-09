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

namespace Chevere\Tests\Spec\_resources\src;

use Chevere\Components\Controller\Controller;
use Chevere\Components\Parameter\ParameterRequired;
use Chevere\Components\Parameter\Parameters;
use Chevere\Components\Plugin\PluggableAnchors;
use Chevere\Components\Plugin\Plugs\Hooks\Traits\PluggableHooksTrait;
use Chevere\Components\Regex\Regex;
use Chevere\Components\Response\ResponseSuccess;
use Chevere\Interfaces\Parameter\ArgumentsInterface;
use Chevere\Interfaces\Parameter\ParametersInterface;
use Chevere\Interfaces\Plugin\PluggableAnchorsInterface;
use Chevere\Interfaces\Plugin\Plugs\Hooks\PluggableHooksInterface;
use Chevere\Interfaces\Response\ResponseInterface;

class TestController extends Controller implements PluggableHooksInterface
{
    use PluggableHooksTrait;

    protected array $_data;

    public static function getHookAnchors(): PluggableAnchorsInterface
    {
        return (new PluggableAnchors)
            ->withAdded('getParameters:after')
            ->withAdded('run:before')
            ->withAdded('run:after');
    }

    public function getParameters(): ParametersInterface
    {
        $parameters = (new Parameters)
            ->withAdded(
                (new ParameterRequired('name'))
                    ->withRegex(new Regex('/^[\w]+$/'))
            )
            ->withAdded(
                (new ParameterRequired('id'))
                    ->withRegex(new Regex('/^[0-9]+$/'))
            );
        $this->hook('getParameters:after', $parameters);

        return $parameters;
    }

    public function run(ArgumentsInterface $arguments): ResponseInterface
    {
        $this->hook('run:before', $arguments);
        $response = new ResponseSuccess([]);
        $data = [
            'userName' => $arguments->get('name'),
            'userId' => $arguments->get('id')
        ];
        $response = $response->withData($data);
        $this->hook('run:after', $response);

        return $response;
    }
}
