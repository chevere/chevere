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

namespace Chevere\Api;

use Chevere\Controllers\Api\OptionsController;
use Chevere\Controllers\Api\HeadController;
use Chevere\Contracts\Api\src\EndpointContract;
use Chevere\Contracts\HttpFoundation\MethodsContract;
use Chevere\HttpFoundation\Method;

final class Endpoint implements EndpointContract
{
    /** @var array */
    private $array;

    /** @var MethodsContract */
    private $methods;

    public function __construct(MethodsContract $methods)
    {
        $this->array = [];
        $this->methods = $methods;
        $this->fillEndpointOptions($this->array);
        $this->autofillMissingOptionsHead($this->array);
    }

    public function methods(): MethodsContract
    {
        return $this->methods;
    }

    public function toArray(): array
    {
        return $this->array;
    }

    public function setResource(array $resource): void
    {
        $this->array['resource'] = $resource;
    }

    private function fillEndpointOptions(array &$endpointApi): void
    {
        foreach ($this->methods as $method) {
            $httpMethod = $method->method();
            $controllerClassName = $method->controller();
            $httpMethodOptions = [];
            $httpMethodOptions['description'] = $controllerClassName::description();
            $controllerParameters = $controllerClassName::parameters();
            if (isset($controllerParameters)) {
                $httpMethodOptions['parameters'] = $controllerParameters;
            }
            $endpointApi['OPTIONS'][$httpMethod] = $httpMethodOptions;
        }
    }

    private function autofillMissingOptionsHead(array &$endpointApi): void
    {
        foreach ([
            'OPTIONS' => [
                OptionsController::class, [
                    'description' => OptionsController::description(),
                ],
            ],
            'HEAD' => [
                HeadController::class, [
                    'description' => HeadController::description(),
                ],
            ],
        ] as $k => $v) {
            if (!$this->methods->has($k)) {
                $this->methods->add(new Method($k, $v[0]));
                $endpointApi['OPTIONS'][$k] = $v[1];
            }
        }
    }
}
