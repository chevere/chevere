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

namespace Chevere\Components\Router;

use Chevere\Components\Message\Message;
use Chevere\Components\Router\Exceptions\RouteIdentifierException;
use Chevere\Components\Router\Interfaces\RouteIdentifierInterface;
use Chevere\Components\Str\Exceptions\StrAssertException;
use Chevere\Components\Str\StrAssert;

final class RouteIdentifier implements RouteIdentifierInterface
{
    private int $id;

    private string $group;

    private string $name;

    /**
     *
     * @throws RouteIdentifierException When passing a negative $id or empty strings.
     */
    public function __construct(int $id, string $group, string $name)
    {
        $this->id = $id;
        $this->assertId();
        $this->group = $group;
        $this->assertString('group');
        $this->name = $name;
        $this->assertString('name');
    }

    public function id(): int
    {
        return $this->id;
    }

    public function group(): string
    {
        return $this->group;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'group' => $this->group,
            'name' => $this->name,
        ];
    }

    private function assertId(): void
    {
        if ($this->id < 0) {
            throw new RouteIdentifierException(
                (new Message('Argument %argumentName% must be bigger than zero (0).'))
                    ->code('%argumentName%', '$id')
                    ->toString()
            );
        }
    }

    private function assertString(string $argumentName): void
    {
        try {
            (new StrAssert($this->$argumentName))
                ->notEmpty()
                ->notCtypeSpace();
        } catch (StrAssertException $e) {
            throw new RouteIdentifierException(
                (new Message('Argument %argumentName% must not be empty neither ctype-space.'))
                    ->code('%argumentName%', '$' . $argumentName)
                    ->toString()
            );
        }
    }
}
