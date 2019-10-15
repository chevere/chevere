<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\Api;

use Chevere\Components\Controllers\Api\HeadController;
use Chevere\Components\Controllers\Api\OptionsController;
use Chevere\Components\Http\Method;
use Chevere\Contracts\Api\src\EndpointContract;
use Chevere\Contracts\Http\MethodsContract;

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
        $this->fillEndpointOptions();
        $this->autofillMissingOptionsHead();
    }

    public function methods(): MethodsContract
    {
        return $this->methods;
    }

    public function toArray(): array
    {
        return $this->array;
    }

    private function fillEndpointOptions(): void
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
            $this->array['OPTIONS'][$httpMethod] = $httpMethodOptions;
        }
    }

    private function autofillMissingOptionsHead(): void
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
                $this->methods = $this->methods
                    ->withAddedMethod(
                        (new Method($k))
                            ->withController($v[0])
                    );
                $this->array['OPTIONS'][$k] = $v[1];
            }
        }
    }
}
