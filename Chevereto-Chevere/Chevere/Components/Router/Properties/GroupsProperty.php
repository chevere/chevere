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

    /** @var Message */
    private $message;

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
        $this->message = (new Message('Expected type %expected%, type %found% found for %at%'));
        foreach ($this->value as $group => $ids) {
            $this->assertGroupKey($group);
            $this->assertIds($group, $ids);
        }
    }

    private function assertGroupKey($val): void
    {
        if (!is_string($val)) {
            throw new RouterPropertyException(
                $this->message
                    ->code('%expected%', 'string')
                    ->code('%found%', gettype($val))
                    ->code('%at%', '(key)')
                    ->toString()
            );
        }
    }

    private function assertIds(string $group, $val): void
    {
        if (!is_array($val)) {
            throw new RouterPropertyException(
                $this->message
                    ->code('%expected%', 'array')
                    ->code('%found%', gettype($val))
                    ->code('%at%', 'key:' . $group)
                    ->toString()
            );
        }
        foreach ($val as $key => $id) {
            if (!is_int($id)) {
                throw new RouterPropertyException(
                    $this->message
                        ->code('%expected%', 'int')
                        ->code('%found%', gettype($id))
                        ->code('%at%', "($group:" . (string) $key . ')')
                        ->toString()
                );
            }
        }
    }
}
