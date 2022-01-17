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

namespace Chevere\Tests\VarSupport\_resources;

use function Chevere\Components\Filesystem\fileForPath;
use Chevere\Interfaces\Filesystem\FileInterface;

final class ClassWithResource
{
    private array $array;

    private FileInterface $file;

    public function __construct($resource)
    {
        $this->array = [$resource];
        $this->file = fileForPath(__FILE__);
    }
}
