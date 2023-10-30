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

namespace Chevere\Parameter;

use Chevere\Parameter\Interfaces\BoolParameterInterface;
use Chevere\Parameter\Traits\ParameterTrait;
use Chevere\Parameter\Traits\SchemaTrait;
use Chevere\Type\Interfaces\TypeInterface;
use function Chevere\Type\typeBool;

final class BoolParameter implements BoolParameterInterface
{
    use ParameterTrait;
    use SchemaTrait;

    private ?bool $default;

    public function withDefault(bool $value): BoolParameterInterface
    {
        $new = clone $this;
        $new->default = $value;

        return $new;
    }

    public function default(): ?bool
    {
        return $this->default ?? null;
    }

    /**
     * @codeCoverageIgnore
     */
    public function assertCompatible(BoolParameterInterface $parameter): void
    {
    }

    private function getType(): TypeInterface
    {
        return typeBool();
    }
}