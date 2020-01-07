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

namespace Chevere\Components\Path;

use Chevere\Components\Message\Message;
use Chevere\Components\Path\Exceptions\PathInvalidException;

final class CheckFormat
{
    private string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
        $this->assertNoDoubleDots();
        $this->assertNoExtraSlashes();
    }

    private function assertNoDoubleDots(): void
    {
        if (false !== strpos($this->path, '../')) {
            throw new PathInvalidException(
                (new Message('Must omit %chars% for path %path%'))
                    ->code('%chars%', '../')
                    ->code('%path%', $this->path)
                    ->toString()
            );
        }
    }

    private function assertNoExtraSlashes(): void
    {
        if (false !== strpos($this->path, '//')) {
            throw new PathInvalidException(
                (new Message('Path %path% contains extra-slashes'))
                    ->code('%path%', $this->path)
                    ->toString()
            );
        }
    }
}
