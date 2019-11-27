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
use Chevere\Components\Router\Properties\Traits\AssertsTrait;
use Chevere\Components\Router\Properties\Traits\ToArrayTrait;
use Chevere\Contracts\Router\Properties\GroupsPropertyContract;
use TypeError;

final class GroupsProperty implements GroupsPropertyContract
{
    use ToArrayTrait;
    use AssertsTrait;

    /** @var Message */
    private $message;

    /**
     * {@inheritdoc}
     */
    public function __construct(array $groups)
    {
        try {
            $this->assertArrayNotEmpty($groups);
            $this->value = $groups;
            $this->asserts();
        } catch (TypeError $e) {
            throw new RouterPropertyException(
                (new Message('Expected type %expected%, type %found% found for %at%'))
                    ->code('%expected%', 'array')
                    // ->code('%found%', gettype($ids))
                    // ->code('%at%', 'group:' . $group)
                    ->toString()
            );
        }
    }

    private function asserts(): void
    {
        foreach ($this->value as $group => $ids) {
            $this->assertString($group);
            $this->assertArrayNotEmpty($ids);
            $this->assertIds($ids);
        }
    }

    private function assertIds($ids): void
    {
        foreach ($ids as $key => $id) {
            if (!is_int($id)) {
                throw new TypeError(
                    $this->message
                        ->code('%expected%', 'int')
                        ->code('%found%', gettype($id))
                        // ->code('%at%', "($group:" . (string) $key . ')')
                        ->toString()
                );
            }
        }
    }
}
