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

use Chevere\Components\Router\Properties\Traits\ToArrayTrait;
use Chevere\Contracts\Router\Properties\NamedPropertyContract;

final class NamedProperty extends PropertyBase implements NamedPropertyContract
{
    use ToArrayTrait;

    /**
     * {@inheritdoc}
     */
    public function __construct(array $named)
    {
        $this->value = $named;
        $this->tryAsserts();
    }

    /**
     * {@inheritdoc}
     */
    protected function asserts(): void
    {
        foreach ($this->value as $name => $id) {
            $this->breadcrum = $this->breadcrum
                ->withAddedItem((string) $name);
            $pos = $this->breadcrum->pos();
            $this->assertString($name);
            $this->breadcrum = $this->breadcrum
                ->withAddedItem((string) $id);
            $posId = $this->breadcrum->pos();
            $this->assertInt($id);
            $this->breadcrum = $this->breadcrum
                ->withRemovedItem($posId)
                ->withRemovedItem($pos);
        }
    }
}
