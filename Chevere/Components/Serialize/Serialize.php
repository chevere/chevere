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
use Chevere\Components\Variable\Interfaces\VariableExportInterface;

final class Serialize implements SerializeInterface
{
    /** @var string */
    private string $serialized;

    /**
     * Creates a new instance.
     */
    public function __construct(VariableExportInterface $variableExport)
    {
        $this->serialized = $variableExport->toSerialize();
    }

    public function toString(): string
    {
        return $this->serialized;
    }
}
