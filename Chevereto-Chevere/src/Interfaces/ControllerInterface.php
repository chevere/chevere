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

namespace Chevere\Interfaces;

use Chevere\HttpFoundation\Response;

interface ControllerInterface
{
    public function __invoke();

    public function setResponse(Response $response): ControllerInterface;

    /**
     * Invoke another controller.
     *
     * @param string $controller Path handle. Start with @, to use the caller dir as root context.
     * @param mixed  $parameters invoke pararameters
     *
     * @return mixed output array or whatever the controller may output
     */
    public function invoke(string $controller, ...$parameters);

    /**
     * Provides access to the Controller $description static.
     *
     * @return string|null
     */
    public static function getDescription(): ?string;

    /**
     * Provides access to the Controller $resources static.
     *
     * @return array|null
     */
    public static function getResources(): ?array;

    /**
     * Provides access to the Controller $parameters static.
     *
     * @return array|null
     */
    public static function getParameters(): ?array;
}
