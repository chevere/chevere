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
use Chevere\Components\Router\Exceptions\RouterPropertyException;
use Chevere\Components\Router\Properties\Traits\AssertArrayTrait;
use Chevere\Components\Router\Properties\Traits\ToArrayTrait;
use Chevere\Contracts\Router\Properties\GroupsPropertyContract;

final class GroupsProperty implements GroupsPropertyContract
{
    use ToArrayTrait;
    use AssertArrayTrait;

    /**
     * {@inheritdoc}
     */
    public function __construct(array $groups)
    {
        $this->value = $groups;
        $this->assertArray();
        $this->assert();
    }

    private function assert(): void
    {
        foreach ($this->value as $group => $ids) {
            $gettype = gettype($group);
            $message = (new Message('Expected type %expected%, type %found% found for %at%'));
            if (!is_string($group)) {
                throw new RouterPropertyException(
                    $message
                        ->code('%expected%', 'string')
                        ->code('%found%', $gettype)
                        ->code('%at%', '(key)')
                        ->toString()
                );
            }
            if (!is_array($ids)) {
                throw new RouterPropertyException(
                    $message
                        ->code('%expected%', 'array')
                        ->code('%found%', $gettype)
                        ->code('%at%', 'key:' . $group)
                        ->toString()
                );
            }
            foreach ($ids as $key => $id) {
                if (!is_int($id)) {
                    throw new RouterPropertyException(
                        $message
                            ->code('%expected%', 'int')
                            ->code('%found%', gettype($id))
                            ->code('%at%', "($group:" . (string) $key . ')')
                            ->toString()
                    );
                }
            }
        }
    }
}
