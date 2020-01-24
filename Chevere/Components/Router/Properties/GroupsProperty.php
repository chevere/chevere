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

namespace Chevere\Components\Router\Properties;

use TypeError;
use Chevere\Components\Router\Properties\Traits\ToArrayTrait;
use Chevere\Components\Router\Interfaces\Properties\GroupsPropertyInterface;

final class GroupsProperty extends PropertyBase implements GroupsPropertyInterface
{
    use ToArrayTrait;

    /**
     * Creates a new instance.
     *
     * @param array $groups Group routes [(string)$group => (int)$id[]]
     *
     * @throws RouterPropertyException if the value doesn't match the property format
     */
    public function __construct(array $groups)
    {
        $this->value = $groups;
        $this->tryAsserts();
    }

    protected function asserts(): void
    {
        $this->assertArrayNotEmpty($this->value);
        $this->breadcrum = $this->breadcrum
            ->withAddedItem('array');
        foreach ($this->value as $group => $ids) {
            $this->breadcrum = $this->breadcrum
                ->withAddedItem((string) $group);
            $pos = $this->breadcrum->pos();
            $this->assertString($group);
            $this->breadcrum = $this->breadcrum
                ->withAddedItem('array');
            $this->assertArrayNotEmpty($ids);
            $idsPos = $this->breadcrum->pos();
            $this->assertIds($ids);
            $this->breadcrum = $this->breadcrum
                ->withRemovedItem($idsPos)
                ->withRemovedItem($pos);
        }
    }

    private function assertIds($ids): void
    {
        foreach ($ids as $key => $id) {
            $this->breadcrum = $this->breadcrum
                ->withAddedItem((string) $key);
            $pos = $this->breadcrum->pos();
            if (!is_int($id)) {
                throw new TypeError(
                    $this->getBadTypeMessage()
                        ->code('%for%', 'id')
                        ->code('%expected%', 'int')
                        ->code('%provided%', gettype($id))
                        ->toString()
                );
            }
            $this->breadcrum = $this->breadcrum
                ->withRemovedItem($pos);
        }
    }
}
