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

namespace Chevere\Tests\Action\_resources\src;

use Chevere\Controller\Controller;
use Chevere\Parameter\Attributes\ParameterAttribute;
use Chevere\Parameter\Interfaces\ParametersInterface;
use Chevere\Parameter\Parameters;
use Chevere\Parameter\StringParameter;

final class ActionTestController extends Controller
{
    public function getResponseParameters(): ParametersInterface
    {
        return new Parameters(user: new StringParameter());
    }

    public function run(
        #[ParameterAttribute(description: 'The username.', regex: '/^[a-zA-Z]+$/')]
        string $name
    ): array {
        return [
            'user' => 'PeoplesHernandez'
        ];
    }
}
