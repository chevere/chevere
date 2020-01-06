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

namespace Chevere\Components\Globals;

use Chevere\Components\App\Instances\BootstrapInstance;
use Chevere\Components\Globals\Exceptions\GlobalsKeyException;
use Chevere\Components\Globals\Exceptions\GlobalsTypeException;
use Chevere\Components\Message\Message;
use Chevere\Contracts\Globals\GlobalsContract;
use TypeError;

/**
 * Provides read-only access for superglobals.
 */
final class Globals implements GlobalsContract
{
    private int $argc;

    private array $argv;

    private array $server;

    private array $get;

    private array $post;

    private array $files;

    private array $cookie;

    private array $session;

    private string $request;

    private array $env;

    private array $globals;

    public function __construct(array $globals)
    {
        $this->globals = $globals;
        $this->assertValidKeys();
        for ($i = 0; $i < count(static::KEYS); $i++) {
            $property = static::METHODS[$i];
            $name = static::KEYS[$i];
            try {
                $this->$property = $globals[$name];
            } catch (TypeError $e) {
                throw new GlobalsTypeException(
                    (new Message('Global key %name% TypeError: %message%'))
                        ->code('%name%', $name)
                        ->strtr('%message%', $e->getMessage())
                        ->toString()
                );
            }
        }
    }

    public function withArgc(int $argc): GlobalsContract
    {
        $new = clone $this;
        $new->argc = $argc;

        return $new;
    }

    public function argc(): int
    {
        return $this->argc;
    }

    public function withArgv(array $argv): GlobalsContract
    {
        $new = clone $this;
        $new->argv = $argv;

        return $new;
    }

    public function argv(): array
    {
        return $this->argv;
    }

    public function withServer(array $server): GlobalsContract
    {
        $new = clone $this;
        $new->server = $server;

        return $new;
    }

    public function server(): array
    {
        return $this->server;
    }

    public function withGet(array $get): GlobalsContract
    {
        $new = clone $this;
        $new->get = $get;

        return $new;
    }

    public function get(): array
    {
        return $this->get;
    }

    public function withPost(array $post): GlobalsContract
    {
        $new = clone $this;
        $new->post = $post;

        return $new;
    }

    public function post(): array
    {
        return $this->post;
    }

    public function withFiles(array $files): GlobalsContract
    {
        $new = clone $this;
        $new->files = $files;

        return $new;
    }

    public function files(): array
    {
        return $this->files;
    }

    public function withCookie(array $cookie): GlobalsContract
    {
        $new = clone $this;
        $new->cookie = $cookie;

        return $new;
    }

    public function cookie(): array
    {
        return $this->cookie;
    }

    public function withSession(array $session): GlobalsContract
    {
        $new = clone $this;
        $new->session = $session;

        return $new;
    }

    public function session(): array
    {
        return $this->session;
    }

    public function withRequest(string $request): GlobalsContract
    {
        $new = clone $this;
        $new->request = $request;

        return $new;
    }

    public function request(): string
    {
        return $this->request;
    }

    public function withEnv(array $env): GlobalsContract
    {
        $new = clone $this;
        $new->env = $env;

        return $new;
    }

    public function env(): array
    {
        return $this->env;
    }

    public function withGlobals(array $globals): GlobalsContract
    {
        $new = clone $this;
        $new->globals = $globals;

        return $new;
    }

    public function globals(): array
    {
        return $this->globals;
    }

    private function fillMissing(): void
    {
        if (!isset($this->globals['_SESSION']) && BootstrapInstance::get()->cli()) {
            $this->globals['_SESSION'] = [];
        }
        if (!isset($this->request)) {
            $this->globals['_REQUEST'] = '';
        }
    }

    private function assertValidKeys(): void
    {
        $this->fillMissing();
        $keys = array_keys($this->globals);
        $diff = array_diff(static::KEYS, $keys);
        if (!empty($diff)) {
            throw new GlobalsKeyException(
                (new Message('Invalid %globals% array passed, missing (%keysCount%) keys: %keys%'))
                    ->code('%globals%', '$GLOBALS')
                    ->code('%keysCount%', count($diff))
                    ->code('%keys%', implode(', ', $diff))
                    ->toString()
            );
        }
    }
}
