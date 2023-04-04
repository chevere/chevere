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

namespace Chevere\HttpController;

use Chevere\HttpController\Interfaces\FileDeliveryMapInterface;

final class FileDeliverMapping implements FileDeliveryMapInterface
{
    public function __construct(
        private string $basename = self::BASENAME,
        private string $pathname = self::PATHNAME,
    ) {
    }

    public function basename(): string
    {
        return $this->basename;
    }

    public function pathname(): string
    {
        return $this->pathname;
    }
}
