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

namespace Chevere\Tests\Controller\_resources\src;

use Chevere\Components\Controller\Controller;
use Chevere\Components\Parameter\Parameters;
use Chevere\Components\Parameter\StringParameter;
use Chevere\Components\Pluggable\Plug\Hook\Traits\PluggableHooksTrait;
use Chevere\Components\Pluggable\PluggableAnchors;
use Chevere\Interfaces\Parameter\ArgumentsInterface;
use Chevere\Interfaces\Parameter\ParametersInterface;
use Chevere\Interfaces\Pluggable\Plug\Hook\PluggableHooksInterface;
use Chevere\Interfaces\Pluggable\PluggableAnchorsInterface;
use Chevere\Interfaces\Response\ResponseInterface;

final class ControllerTestController extends Controller implements PluggableHooksInterface
{
    use PluggableHooksTrait;

    public function getParameters(): ParametersInterface
    {
        $parameters = new Parameters(
            string: new StringParameter()
        );

        $this->hook('test', $parameters);

        return $parameters;
    }

    public static function getHookAnchors(): PluggableAnchorsInterface
    {
        return new PluggableAnchors('test');
    }

    public function run(ArgumentsInterface $arguments): ResponseInterface
    {
        return $this->getResponse();
    }
}
