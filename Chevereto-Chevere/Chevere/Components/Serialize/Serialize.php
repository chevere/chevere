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

use Chevere\Contracts\Serialize\SerializeContract;
use Chevere\Contracts\Variable\VariableExportableContract;

final class Serialize implements SerializeContract
{
    /** @var string */
    private $serialized;

    /**
     * {@inheritdoc}
     */
    public function __construct(VariableExportableContract $variable)
    {
        $this->serialized = $variable->toSerialize();
    }

    /**
     * {@inheritdoc}
     */
    public function toString(): string
    {
        return $this->serialized;
    }
}
