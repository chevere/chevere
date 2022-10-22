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

namespace Chevere\Tests\Controller\_resources;

use Chevere\Controller\HttpController;
use function Chevere\Parameter\arrayParameter;
use Chevere\Parameter\Interfaces\ParametersInterface;
use function Chevere\Parameter\parameters;
use function Chevere\Parameter\stringParameter;

final class TestHttpController extends HttpController
{
    public function acceptGet(): ParametersInterface
    {
        return parameters(
            ...[
                'foo-foo' => stringParameter('/^[a-z]+$/'),
            ]
        );
    }

    public function acceptPost(): ParametersInterface
    {
        return parameters(
            ...[
                'bar.bar' => stringParameter('/^[1-9]+$/'),
            ]
        );
    }

    public function acceptFiles(): ParametersInterface
    {
        return parameters(
            ...[
                'MyFile!' => arrayParameter(),
            ]
        );
    }

    public function run(): array
    {
        return [];
    }
}
