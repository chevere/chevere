<?php

declare(strict_types=1);

/*
 * This file is part of Chevereto\Core.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevereto\Core\Interfaces;

use Chevereto\Core\Route;
use Chevereto\Core\Response;
use Chevereto\Core\App;
use Chevereto\Core\Api;

interface ControllerInterface
{
    public function __invoke();

    public function getRoute(): ?Route;

    public function getApi(): ?Api;

    public function setResponse(Response $response): ControllerInterface;

    public function getResponse(): ?Response;

    public function setApp(App $app): ControllerInterface;

    public function getApp(): App;

    /**
     * Invoke another controller.
     *
     * @param string $controller Path handle. Start with @, to use the caller dir as root context.
     * @param mixed  $parameters invoke pararameter or parameters (array)
     *
     * @return mixed output array or whatever the controller may output
     */
    public function invoke(string $controller, $parameters = null);
}
