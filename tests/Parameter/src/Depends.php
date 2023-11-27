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

namespace Chevere\Tests\Parameter\src;

use Chevere\Parameter\Attributes\StringAttr;
use stdClass;

final class Depends
{
    public function useObject(stdClass $file)
    {
    }

    public function useString(
        #[StringAttr('/^[a-z]+$/', description: 'A string')]
        string $string = 'default'
    ) {
    }

    public function useUnion(string|int $union)
    {
    }

    public function useIntersection(stdClass&Depends $intersection)
    {
    }
}
