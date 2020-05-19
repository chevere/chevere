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

namespace Chevere\Components\Serialize;

use Chevere\Components\Serialize\Interfaces\SerializeInterface;
use Chevere\Components\VarExportable\Interfaces\VarExportableInterface;

final class Serialize implements SerializeInterface
{
    /** @var string */
    private string $serialized;

    public function __construct(VarExportableInterface $varExportable)
    {
        $this->serialized = $varExportable->toSerialize();
    }

    public function toString(): string
    {
        return $this->serialized;
    }
}
