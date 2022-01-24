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

use Chevere\Controller\Controller;
use Chevere\Parameter\Interfaces\ArgumentsInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;
use Chevere\Parameter\Parameters;
use Chevere\Parameter\StringParameter;
use Chevere\Pluggable\Interfaces\Plug\Hook\PluggableHooksInterface;
use Chevere\Pluggable\Interfaces\PluggableAnchorsInterface;
use Chevere\Pluggable\Plug\Hook\Traits\PluggableHooksTrait;
use Chevere\Pluggable\PluggableAnchors;
use Chevere\Response\Interfaces\ResponseInterface;

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
