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

namespace Chevere\Parameter\Traits;

use Chevere\Type\Interfaces\TypeInterface;

trait ParameterTrait
{
    private TypeInterface $type;

    final public function __construct(
        private string $description = ''
    ) {
        $this->setUp();
        $this->type = $this->type();
    }

    public function setUp(): void
    {
        // Nothing to do
    }

    /**
     * @infection-ignore-all
     */
    final public function type(): TypeInterface
    {
        return $this->type ??= $this->getType();
    }

    final public function description(): string
    {
        return $this->description;
    }

    final public function withDescription(string $description): static
    {
        $new = clone $this;
        $new->description = $description;

        return $new;
    }

    abstract private function getType(): TypeInterface;
}
