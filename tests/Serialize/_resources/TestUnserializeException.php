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

namespace Chevere\Tests\Serialize\_resources;

use Exception;

final class TestUnserializeException
{
    public string $prop = 'test';

    public function __unserialize(array $data): void
    {
        throw new Exception('Error!');
    }
}
