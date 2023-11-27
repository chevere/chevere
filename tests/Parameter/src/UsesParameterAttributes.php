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

use Chevere\Parameter\Attributes\ArrayAttr;
use Chevere\Parameter\Attributes\GenericAttr;
use Chevere\Parameter\Attributes\IntAttr;
use Chevere\Parameter\Attributes\StringAttr;
use function Chevere\Parameter\Attributes\arrayAttr;
use function Chevere\Parameter\Attributes\genericAttr;
use function Chevere\Parameter\Attributes\intAttr;
use function Chevere\Parameter\Attributes\stringAttr;

final class UsesParameterAttributes
{
    public function __construct(
        #[StringAttr('/^[A-Za-z]+$/')]
        string $name = '',
        #[IntAttr(minimum: 1, maximum: 100)]
        int $age = 12,
        #[ArrayAttr(
            id: new IntAttr(minimum: 1),
        )]
        array $array = [],
        #[GenericAttr(
            new StringAttr('/^[A-Za-z]+$/'),
        )]
        iterable $iterable = [],
    ) {
        stringAttr('name')($name);
        intAttr('age')($age);
        arrayAttr('array')($array);
        genericAttr('iterable')($iterable);
    }
}
