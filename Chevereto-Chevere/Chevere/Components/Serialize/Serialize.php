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

namespace Chevere\Components\Serialize;

use Chevere\Components\Serialize\Contracts\SerializeContract;
use Chevere\Components\Variable\Contracts\VariableExportContract;

final class Serialize implements SerializeContract
{
    /** @var string */
    private string $serialized;

    /**
     * Creates a new instance.
     */
    public function __construct(VariableExportContract $variableExport)
    {
        $this->serialized = $variableExport->toSerialize();
    }

    /**
     * {@inheritdoc}
     */
    public function toString(): string
    {
        return $this->serialized;
    }
}
