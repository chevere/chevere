<?php

declare(strict_types=1);
/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevereto\Chevere\Interfaces;

use Chevereto\Chevere\Runtime;
use Monolog\Logger;
use Chevereto\Chevere\Router;
use Chevereto\Chevere\HttpRequest;
use Chevereto\Chevere\Response;
use Chevereto\Chevere\Api;
use Chevereto\Chevere\Route;
use Chevereto\Chevere\Handler;

interface AppInterface
{
    public function setArguments(array $arguments): AppInterface;

    public function getArguments(): ?array;

    public function setControllerArguments(array $arguments): AppInterface;

    public function getControllerArguments(): ?array;

    public function getRuntime(): ?Runtime;

    public function getLogger(): ?Logger;

    public function getRouter(): ?Router;

    public function getHttpRequest(): ?HttpRequest;

    public function getResponse(): ?Response;

    public function getApi(): ?Api;

    public function getRoute(): ?Route;

    public function getHandler(): ?Handler;
}
