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
use Chevere\Components\Router\Contracts\Properties\NamedPropertyContract;

final class NamedProperty extends PropertyBase implements NamedPropertyContract
{
    use ToArrayTrait;

    /**
     * Creates a new instance.
     *
     * @param array $named Named routes [(string)$name => (int)$id]
     *
     * @throws RouterPropertyException if the value doesn't match the property format
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
            $this->assertInt($id);
            $this->breadcrum = $this->breadcrum
                ->withRemovedItem($pos);
        }
    }
}
