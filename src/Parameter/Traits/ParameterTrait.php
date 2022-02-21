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

use Chevere\Common\Traits\DescriptionTrait;
use Chevere\Type\Interfaces\TypeInterface;

trait ParameterTrait
{
    use DescriptionTrait;

    private TypeInterface $type;

    abstract public function getType(): TypeInterface;

    public function setUp(): void
    {
        // Nothing to do
    }

    final public function __construct(
        private string $description = ''
    ) {
        $this->setUp();
        $this->type = $this->type();
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
}
