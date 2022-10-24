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

namespace Chevere\Controller\Interfaces;

use Psr\Http\Message\UriInterface;

/**
 * Describes the component in charge of defining a HTTP Redirect Controller.
 */
interface HttpRedirectControllerInterface extends HttpControllerInterface
{
    public const STATUSES = [
        300 => 'Multiple Choice',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
    ];

    public function withUri(UriInterface $uri): static;

    public function withStatus(int $status): static;

    public function uri(): UriInterface;

    public function status(): int;
}
