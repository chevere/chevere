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

namespace Chevere\Components\Globals\Interfaces;

interface GlobalsInterface
{
    const PROPERTIES = [
        'argc',
        'argv',
        'server',
        'get',
        'post',
        'files',
        'cookie',
        'session',
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
    ];

    const DEFAULTS = [
        0,
        [],
        [],
        [],
        [],
        [],
        [],
        [],
    ];

    public function __construct(array $globals);

    public function argc(): int;

    public function argv(): array;

    public function server(): array;

    public function get(): array;

    public function post(): array;

    public function files(): array;

    public function cookie(): array;

    public function session(): array;

    public function globals(): array;
}
