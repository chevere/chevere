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

use Chevere\Components\Controller\ControllerName;
use Chevere\Components\Controllers\Api\HeadController;
use Chevere\Components\Controllers\Api\OptionsController;
use Chevere\Components\Http\Method;
use Chevere\Components\Http\MethodControllerName;
use Chevere\Contracts\Api\src\EndpointContract;
use Chevere\Components\Http\Contracts\MethodControllerNameCollectionContract;

final class Endpoint implements EndpointContract
{
    /** @var array */
    private $array;

    /** @var MethodControllerNameCollectionContract */
    private $methodControllerNameCollection;

    /**
     * {@inheritdoc}
     */
    public function __construct(MethodControllerNameCollectionContract $collection)
    {
        $this->array = [];
        $this->methodControllerNameCollection = $collection;
        $this->fillEndpointOptions();
        $this->autofillMissingOptionsHead();
    }

    /**
     * {@inheritdoc}
     */
    public function methodControllerNameCollection(): MethodControllerNameCollectionContract
    {
        return $this->methodControllerNameCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return $this->array;
    }

    private function fillEndpointOptions(): void
    {
        foreach ($this->methodControllerNameCollection->toArray() as $method) {
            $httpMethod = $method->method();
            $controllerClassName = $method->controllerName();
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
            if (!$this->methodControllerNameCollection->has(new Method($k))) {
                $this->methodControllerNameCollection = $this->methodControllerNameCollection
                    ->withAddedMethodControllerName(
                        new MethodControllerName(
                            new Method($k),
                            new ControllerName($v[0])
                        )
                    );
                $this->array['OPTIONS'][$k] = $v[1];
            }
        }
    }
}
