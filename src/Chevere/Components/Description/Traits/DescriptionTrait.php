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

namespace Chevere\Components\Description\Traits;

/**
 * @codeCoverageIgnore
 */
trait DescriptionTrait
{
    protected string $description = '';

    public function getDescription(): string
    {
        return '';
    }

    public function description(): string
    {
        return $this->description;
    }
}
