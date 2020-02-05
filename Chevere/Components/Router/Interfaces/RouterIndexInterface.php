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

namespace Chevere\Components\Router\Interfaces;

use Chevere\Components\Route\Interfaces\PathUriInterface;
use Chevere\Components\Route\PathUri;

interface RouterIndexInterface
{
    public function withAdded(PathUriInterface $pathUri, int $id, string $group, string $name): RouterIndexInterface;

    public function has(PathUri $pathUri): bool;

    public function get(PathUri $pathUri): array;

    public function toArray(): array;
}
