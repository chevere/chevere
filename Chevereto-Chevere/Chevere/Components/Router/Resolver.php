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

namespace Chevere\Components\Router;

use Chevere\Components\Message\Message;
use Chevere\Contracts\Route\RouteContract;
use InvalidArgumentException;
use TypeError;

final class Resolver
{
    /** @var object */
    private $object;

    /**
     * Creates a new instance.
     *
     * @throws InvalidArgumentException if $serialized can't be unserialized
     * @throws TypeError                if $serialized is not a RouteContract serialize
     */
    public function __construct(string $serialized)
    {
        $object = unserialize($serialized);
        if (false === $object) {
            throw new InvalidArgumentException(
                (new Message('String provided is unable to unserialize'))
                    ->toString()
            );
        }
        if (!is_object($object)) {
            throw new TypeError(
                (new Message('Expecting type %expected%, type %provided% provided'))
                    ->code('%expected%', 'object')
                    ->code('%provided%', gettype($object))
                    ->toString()
            );
        }
        if (!($object instanceof RouteContract)) {
            throw new TypeError(
                (new Message("Instance of %className% doesn't implement %contract%"))
                    ->code('%className%', get_class($object))
                    ->code('%contract%', RouteContract::class)
                    ->toString()
            );
        }
        $this->object = $object;
    }

    /**
     * Provides access to the RouteContract instance.
     */
    public function route(): RouteContract
    {
        return $this->route;
    }
}
