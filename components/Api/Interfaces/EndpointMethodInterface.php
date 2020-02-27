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

namespace Chevere\Components\Api\Interfaces;

use Chevere\Components\Http\Interfaces\RequestInterface;

interface EndpointMethodInterface
{
    public function __construct();

    public function __invoke();

    /**
     * Returns the absolute path to the class file.
     */
    public function whereIs(): string;

    public function setUp(): void;

    public function tearDown(): void;

    // public function withEndpoint(EndpointInterface $endpoint): MethodInterface;

    // public function endpoint(): EndpointInterface;
}
