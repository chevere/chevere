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
use function Chevere\Parameter\arrayParameter;
use function Chevere\Parameter\fileParameter;
use Chevere\Parameter\Interfaces\ArrayTypeParameterInterface;
use function Chevere\Parameter\stringParameter;

final class TestHttpAcceptController extends HttpController
{
    public function acceptQuery(): ArrayTypeParameterInterface
    {
        return arrayParameter(
            ...[
                'foo-foo' => stringParameter('/^[a-z]+$/'),
            ]
        );
    }

    public function acceptBody(): ArrayTypeParameterInterface
    {
        return arrayParameter(
            ...[
                'bar.bar' => stringParameter('/^[1-9]+$/'),
            ]
        );
    }

    public function acceptFiles(): ArrayTypeParameterInterface
    {
        return arrayParameter(
            ...[
                'MyFile!' => fileParameter(
                    type: stringParameter('/^text\/plain$/')
                ),
            ]
        );
    }

    public function run(): array
    {
        return [];
    }
}
