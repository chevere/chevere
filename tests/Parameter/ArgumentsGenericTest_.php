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

namespace Chevere\Tests\Parameter;

use Chevere\Parameter\Arguments;
use function Chevere\Parameter\arrayParameter;
use function Chevere\Parameter\parameters;
use function Chevere\Parameter\stringParameter;
use PHPUnit\Framework\TestCase;

final class ArgumentsGenericTest extends TestCase
{
    public function testName(): void
    {
        $args = [
            'generic' => [
                '_K' => 1111,
                '_V' => 1111,
            ],
        ];
        $parameters = parameters(
            generic: arrayParameter(
                _K: stringParameter(),
                _V: stringParameter(),
            )
        );
        $arguments = new Arguments($parameters, ...$args);
    }
}
