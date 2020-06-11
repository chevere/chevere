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

namespace Chevere\Examples;

use Chevere\Components\Controller\ControllerResponse;
use Chevere\Components\Plugin\PluggableAnchors;
use Chevere\Components\Plugs\Hooks\Traits\PluggableHooksTrait;
use Chevere\Examples\HelloWorldController;
use Chevere\Interfaces\Controller\ControllerArgumentsInterface;
use Chevere\Interfaces\Controller\ControllerResponseInterface;
use Chevere\Interfaces\Plugin\PluggableAnchorsInterface;
use Chevere\Interfaces\Plugs\Hooks\PluggableHooksInterface;

final class HookHelloWorldController extends HelloWorldController implements PluggableHooksInterface
{
    use PluggableHooksTrait;

    public static function getHookAnchors(): PluggableAnchorsInterface
    {
        return (new PluggableAnchors)
            ->withAddedAnchor('beforeResponse');
    }

    public function run(ControllerArgumentsInterface $controllerArguments): ControllerResponseInterface
    {
        $greet = sprintf('Hello, %s', $controllerArguments->get('name'));
        $this->hook('beforeResponse', $greet);

        return (new ControllerResponse(true))
            ->withData([$greet]);
    }
}
