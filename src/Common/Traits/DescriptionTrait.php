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

namespace Chevere\Common\Traits;

/**
 * @codeCoverageIgnore
 */
trait DescriptionTrait
{
    public function getDescription(): string
    {
        return '';
    }

    public function description(): string
    {
        return $this->description ??= $this->getDescription();
    }

    public function withDescription(string $description): static
    {
        $new = clone $this;
        $new->description = $description;

        return $new;
    }
}
