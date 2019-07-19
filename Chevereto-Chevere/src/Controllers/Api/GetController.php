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

namespace Chevereto\Chevere\Controllers\Api;

use const Chevereto\Chevere\CLI;
use Chevereto\Chevere\Console;
use Chevereto\Chevere\Message;
use Chevereto\Chevere\Controller;
use InvalidArgumentException;

/**
 * Exposes an API endpoint.
 */
class GetController extends Controller
{
    const OPTIONS = [
        'description' => 'Retrieve endpoint.',
    ];

    /** @var string */
    protected $endpoint;

    /**
     * @param string $endpoint an API endpoint (/api)
     */
    public function __invoke(?string $endpoint = null)
    {
        if (isset($endpoint)) {
            $route = $this->getApp()->router->resolve($endpoint);
        } else {
            $route = $this->getApp()->route;
            if (isset($route)) {
                $endpoint = $route->getUri();
            } else {
                $message =
                    (string)
                        (new Message('Must provide the %s argument when running this callable without route context.'))
                            ->code('%s', '$endpoint');
                if (CLI) {
                    Console::io()->error($message);

                    return;
                }
                throw new InvalidArgumentException($message);
            }
        }

        if (!isset($route)) {
            $this->getResponse()->setStatusCode(404);

            return;
        }

        $this->endpoint = $endpoint;

        $this->process();
    }

    private function process()
    {
        $endpointData = $this->getApi()->getEndpoint($this->endpoint);
        if ($endpointData) {
            $this->getResponse()->setMeta(['api' => $this->endpoint]);
            foreach ($endpointData as $property => $data) {
                if ($property == 'wildcards') {
                    continue;
                }
                // TODO: Use json:api immutable
                $this->getResponse()->addData('endpoint', $property, $data);
            }
        }
        $this->getResponse()->setStatusCode(200);
    }
}
