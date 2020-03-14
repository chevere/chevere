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

namespace Chevere\Components\Http;

use Chevere\Components\Http\Interfaces\MethodControllerNameInterface;
use SplObjectStorage;

// TODO: Read-only proxy
final class MethodControllerNameObjects extends SplObjectStorage
{
    public function append(MethodControllerNameInterface $methodControllerName, int $id)
    {
        return parent::attach($methodControllerName, $id);
    }

    public function current(): MethodControllerNameInterface
    {
        return parent::current();
    }

    public function getInfo(): int
    {
        return  parent::getInfo();
    }
}
