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

namespace Chevere\Tests\HttpController\_resources;

use Chevere\HttpController\HttpController;
use function Chevere\Parameter\arrayp;
use function Chevere\Parameter\filep;
use Chevere\Parameter\Interfaces\ArrayTypeParameterInterface;
use function Chevere\Parameter\stringp;

final class TestHttpAcceptController extends HttpController
{
    public function acceptQuery(): ArrayTypeParameterInterface
    {
        return arrayp(
            ...[
                'foo-foo' => stringp('/^[a-z]+$/'),
            ]
        );
    }

    public function acceptBody(): ArrayTypeParameterInterface
    {
        return arrayp(
            ...[
                'bar.bar' => stringp('/^[1-9]+$/'),
            ]
        );
    }

    public function acceptFiles(): ArrayTypeParameterInterface
    {
        return arrayp(
            ...[
                'MyFile!' => filep(
                    type: stringp('/^text\/plain$/')
                ),
            ]
        );
    }

    public function run(): array
    {
        return [];
    }
}
