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

namespace Chevere\Contracts\Globals;

interface GlobalsContract
{
    const METHODS = [
        'argc',
        'argv',
        'server',
        'get',
        'post',
        'files',
        'cookie',
        'session',
        'request',
        'env',
    ];

    const KEYS = [
        'argc',
        'argv',
        '_SERVER',
        '_GET',
        '_POST',
        '_FILES',
        '_COOKIE',
        '_SESSION',
        '_REQUEST',
        '_ENV',
    ];

    public function __construct(array $globals);

    public function withArgc(int $argc): GlobalsContract;

    public function argc(): int;

    public function withArgv(array $argv): GlobalsContract;

    public function argv(): array;

    public function withServer(array $server): GlobalsContract;

    public function server(): array;

    public function withGet(array $get): GlobalsContract;

    public function get(): array;

    public function withPost(array $post): GlobalsContract;

    public function post(): array;

    public function withFiles(array $files): GlobalsContract;

    public function files(): array;

    public function withCookie(array $cookie): GlobalsContract;

    public function cookie(): array;

    public function withSession(array $session): GlobalsContract;

    public function session(): array;

    public function withRequest(string $request): GlobalsContract;

    public function request(): string;

    public function withEnv(array $env): GlobalsContract;

    public function env(): array;

    public function withGlobals(array $globals): GlobalsContract;

    public function globals(): array;
}
