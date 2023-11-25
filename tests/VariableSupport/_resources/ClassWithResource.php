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

use Psr\Http\Message\StreamInterface;
use function Chevere\Writer\streamTemp;

final class ClassWithResource
{
    private array $array;

    private StreamInterface $file;

    public function __construct($resource)
    {
        $this->array = [$resource];
        $this->file = streamTemp();
    }
}
