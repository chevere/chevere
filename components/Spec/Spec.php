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

namespace Chevere\Components\Spec;

use Chevere\Components\Message\Message;
use Chevere\Components\Router\Interfaces\RouterInterface;
use Chevere\Components\Spec\Exceptions\RouterMissingPropertyException;
use Chevere\Components\Spec\Interfaces\SpecInterface;

/**
 * The Chevere Spec
 *
 * A collection of application routes and its endpoints.
 */
final class Spec implements SpecInterface
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
        $this->assertRouter();
    }

    private function assertRouter(): void
    {
        foreach ([
            'groups' => $this->router->hasGroups(),
            'index' => $this->router->hasIndex(),
            'named' => $this->router->hasNamed(),
            'regex' => $this->router->hasRegex(),
        ] as $prop => $has) {
            if (!$has) {
                throw new RouterMissingPropertyException(
                    (new Message('Missing %interfaceName% %property% property(s).'))
                        ->code('%interfaceName%', RouterInterface::class)
                        ->code('%property%', $prop)
                        ->toString()
                );
            }
        }
    }
}
