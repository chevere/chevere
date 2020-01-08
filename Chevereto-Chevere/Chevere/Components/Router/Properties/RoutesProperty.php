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

namespace Chevere\Components\Router\Properties;

use Chevere\Components\Message\Message;
use Chevere\Components\Router\Properties\Traits\ToArrayTrait;
use Chevere\Components\Serialize\Unserialize;
use Chevere\Components\Route\Contracts\RouteContract;
use Chevere\Contracts\Router\Properties\RoutesPropertyContract;
use LogicException;

final class RoutesProperty extends PropertyBase implements RoutesPropertyContract
{
    use ToArrayTrait;

    /**
     * @throws RouterPropertyException if the value doesn't match the property format
     */
    public function __construct(array $routes)
    {
        $this->value = $routes;
        $this->tryAsserts();
    }

    /**
     * {@inheritdoc}
     */
    protected function asserts(): void
    {
        $this->assertArrayNotEmpty($this->value);
        foreach ($this->value as $id => $serialized) {
            $this->breadcrum = $this->breadcrum
                ->withAddedItem((string) $id);
            $pos = $this->breadcrum->pos();
            $this->assertInt($id);
            $this->assertStringNotEmpty($serialized);
            $this->assertString($serialized);
            $this->assertSerialized($serialized);
            $this->breadcrum = $this->breadcrum
                ->withRemovedItem($pos);
        }
    }

    private function assertSerialized(string $serialized): void
    {
        $serialize = new Unserialize($serialized);
        if (!($serialize->var() instanceof RouteContract)) {
            throw new LogicException(
                (new Message('Value must be a serialized object implementing the %contract%'))
                    ->code('%contract%', RouteContract::class)
                    ->toString()
            );
        }
    }
}
