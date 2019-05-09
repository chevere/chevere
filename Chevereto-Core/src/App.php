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

namespace Chevereto\Core;

use Monolog\Logger;
use Chevereto\Core\Interfaces\AppInterface;

/**
 * App contains the whole thing.
 */
class App extends AppAbstract implements AppInterface
{
    /**
     * @param array $arguments string arguments captured or injected
     */
    public function setArguments(array $arguments): AppInterface
    {
        $this->arguments = $arguments;

        return $this;
    }

    public function getArguments(): ?array
    {
        return $this->arguments ?? null;
    }

    /**
     * @param array $arguments Prepared controller arguments
     */
    public function setControllerArguments(array $arguments): AppInterface
    {
        $this->controllerArguments = $arguments;

        return $this;
    }

    public function getControllerArguments(): ?array
    {
        return $this->controllerArguments ?? null;
    }

    public function getRuntime(): ?Runtime
    {
        return $this->runtime ?? null;
    }

    public function getLogger(): ?Logger
    {
        return $this->logger ?? null;
    }

    public function getRouter(): ?Router
    {
        return $this->router ?? null;
    }

    public function getHttpRequest(): ?HttpRequest
    {
        return $this->httpRequest ?? null;
    }

    public function getResponse(): ?Response
    {
        return $this->response ?? null;
    }

    public function getApi(): ?Api
    {
        return $this->api ?? null;
    }

    public function getRoute(): ?Route
    {
        return $this->route ?? null;
    }

    public function getHandler(): ?Handler
    {
        return $this->handler ?? null;
    }
}
