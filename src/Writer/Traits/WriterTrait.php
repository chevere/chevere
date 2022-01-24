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

namespace Chevere\Writer\Traits;

use Chevere\Writer\Interfaces\WriterInterface;

/**
 * @codeCoverageIgnore
 * @infection-ignore-all
 */
trait WriterTrait
{
    private WriterInterface $writer;

    public function withWriter(WriterInterface $writer): static
    {
        $new = clone $this;
        $new->writer = $writer;

        return $new;
    }

    public function writer(): WriterInterface
    {
        return $this->writer;
    }
}
