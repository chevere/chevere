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

namespace Chevere\Components\Globals;

use TypeError;
use Chevere\Components\Globals\Exceptions\GlobalsTypeException;
use Chevere\Components\Message\Message;
use Chevere\Components\Globals\Interfaces\GlobalsInterface;

/**
 * Provides read-only access for superglobals.
 */
final class Globals implements GlobalsInterface
{
    private int $argc;

    private array $argv;

    private array $server;

    private array $get;

    private array $post;

    private array $files;

    private array $cookie;

    private array $session;

    private array $globals;

    /**
     * Creates a new instance.
     */
    public function __construct(array $globals)
    {
        $this->globals = [];
        $countKeys = count(GlobalsInterface::KEYS);
        for ($i = 0; $i < $countKeys; $i++) {
            $key = GlobalsInterface::KEYS[$i];
            $this->globals[$key] = GlobalsInterface::DEFAULTS[$i];
            if (array_key_exists($key, $globals)) {
                $this->globals[$key] = $globals[$key];
            }
            $property = GlobalsInterface::PROPERTIES[$i];
            try {
                $this->$property = $this->globals[$key];
            } catch (TypeError $e) {
                throw new GlobalsTypeException(
                    (new Message('Global key %key% TypeError: %message%'))
                        ->code('%key%', $key)
                        ->strtr('%message%', $e->getMessage())
                        ->toString()
                );
            }
        }
    }

    public function argc(): int
    {
        return $this->argc;
    }

    public function argv(): array
    {
        return $this->argv;
    }

    public function server(): array
    {
        return $this->server;
    }

    public function get(): array
    {
        return $this->get;
    }

    public function post(): array
    {
        return $this->post;
    }

    public function files(): array
    {
        return $this->files;
    }

    public function cookie(): array
    {
        return $this->cookie;
    }

    public function session(): array
    {
        return $this->session;
    }

    public function globals(): array
    {
        return $this->globals;
    }
}
