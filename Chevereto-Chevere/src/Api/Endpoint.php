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

namespace Chevereto\Chevere\Api;

use Chevereto\Chevere\Controllers\Api\OptionsController;
use Chevereto\Chevere\Controllers\Api\HeadController;

/**
 * Creates endpoints from ControlerInspect object.
 */
class Endpoint
{
    /** @var array */
    public $array;

    /** @var array */
    public $httpMethods;

    public function __construct(array $httpMethods)
    {
        $this->array = [];
        $this->httpMethods = $httpMethods;
        $this->fillEndpointOptions($this->httpMethods, $this->array);
        $this->autofillMissingOptionsHead($this->httpMethods, $this->array);
    }

    public function getHttpMethods(): array
    {
        return $this->httpMethods;
    }

    public function toArray(): array
    {
        return $this->array;
    }

    public function setResource(?array $resource): void
    {
        $this->array['resource'] = $resource;
    }

    protected function fillEndpointOptions(array &$httpMethods, array &$endpointApi)
    {
        foreach ($httpMethods as $httpMethod => $controllerClassName) {
            $httpMethodOptions = [];
            $httpMethodOptions['description'] = $controllerClassName::getDescription();
            $controllerParameters = $controllerClassName::getParameters();
            if (isset($controllerParameters)) {
                $httpMethodOptions['parameters'] = $controllerParameters;
            }
            $endpointApi['OPTIONS'][$httpMethod] = $httpMethodOptions;
        }
    }

    protected function autofillMissingOptionsHead(array &$httpMethods, array &$endpointApi)
    {
        foreach ([
            'OPTIONS' => [
                OptionsController::class, [
                    'description' => OptionsController::getDescription(),                    ],
            ],
            'HEAD' => [
                HeadController::class, [
                    'description' => HeadController::getDescription(),
                ],
            ],
        ] as $k => $v) {
            if (!isset($httpMethods[$k])) {
                $httpMethods[$k] = $v[0];
                $endpointApi['OPTIONS'][$k] = $v[1];
            }
        }
    }
}
