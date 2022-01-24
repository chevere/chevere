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

use Chevere\Controller\Controller;
use Chevere\Parameter\Interfaces\ArgumentsInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;
use Chevere\Parameter\Parameters;
use Chevere\Parameter\StringParameter;
use Chevere\Pluggable\Interfaces\Plug\Hook\PluggableHooksInterface;
use Chevere\Pluggable\Interfaces\PluggableAnchorsInterface;
use Chevere\Pluggable\Plug\Hook\Traits\PluggableHooksTrait;
use Chevere\Pluggable\PluggableAnchors;
use Chevere\Regex\Regex;
use Chevere\Response\Interfaces\ResponseInterface;
use Chevere\Response\Response;

class TestController extends Controller implements PluggableHooksInterface
{
    use PluggableHooksTrait;

    protected array $_data;

    public static function getHookAnchors(): PluggableAnchorsInterface
    {
        return new PluggableAnchors(
            'getParameters:after',
            'run:before',
            'run:after'
        );
    }

    public function getParameters(): ParametersInterface
    {
        $parameters = new Parameters(
            name: (new StringParameter())
                ->withRegex(new Regex('/^[\w]+$/')),
            id: (new StringParameter())
                ->withRegex(new Regex('/^[0-9]+$/'))
        );
        $this->hook('getParameters:after', $parameters);

        return $parameters;
    }

    public function run(ArgumentsInterface $arguments): ResponseInterface
    {
        $this->hook('run:before', $arguments);
        $response = new Response();
        $data = [
            'userName' => $arguments->get('name'),
            'userId' => $arguments->get('id'),
        ];
        $response = $response->withData(...$data);
        $this->hook('run:after', $response);

        return $response;
    }
}
