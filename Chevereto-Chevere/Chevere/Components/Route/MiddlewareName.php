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

namespace Chevere\Components\Route;

use Chevere\Components\App\Exceptions\MiddlewareContractException;
use Chevere\Components\Message\Message;
use Chevere\Contracts\Middleware\MiddlewareContract;

final class MiddlewareName
{
    /** @var string */
    private $middlewareName;

    public function __construct(string $middlewareName)
    {
        $this->middlewareName = $middlewareName;
        $this->assertMiddlewareContract();
    }

    public function toString(): string
    {
        return $this->middlewareName;
    }

    private function assertMiddlewareContract(): void
    {
        if (is_subclass_of(MiddlewareContract::class, $this->middlewareName)) {
            throw new MiddlewareContractException(
                (new Message('Middleware %middleware% must implement the %contract% contract'))
                    ->code('%middleware%', $this->middlewareName)
                    ->code('%contract%', MiddlewareContract::class)
                    ->toString()
            );
        }
    }
}
