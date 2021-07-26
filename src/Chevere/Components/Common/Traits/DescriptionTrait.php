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

namespace Chevere\Components\Common\Traits;

/**
 * @codeCoverageIgnore
 */
trait DescriptionTrait
{
    //private string $description = ''; @FLAG PHP BUG

    public function getDescription(): string
    {
        return '';
    }

    public function description(): string
    {
        return $this->description ??= $this->getDescription();
    }
}
