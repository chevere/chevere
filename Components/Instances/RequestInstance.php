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

namespace Chevere\Components\Instances;

use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\LogicException;
use Psr\Http\Message\RequestInterface;

/**
 * A container for the request instance.
 */
final class RequestInstance
{
    private static RequestInterface $instance;

    public function __construct(RequestInterface $request)
    {
        self::set($request);
    }

    public static function set(RequestInterface $request): void
    {
        self::$instance = $request;
    }

    public static function type(): string
    {
        return RequestInterface::class;
    }

    public static function get(): RequestInterface
    {
        if (!isset(self::$instance)) {
            throw new LogicException(
                (new Message('No request instance present'))
            );
        }

        return self::$instance;
    }
}
