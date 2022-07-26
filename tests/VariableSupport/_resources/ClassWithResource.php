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

namespace Chevere\Tests\VariableSupport\_resources;

use function Chevere\Filesystem\fileForPath;
use Chevere\Filesystem\Interfaces\FileInterface;

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
