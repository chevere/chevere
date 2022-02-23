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

use Chevere\Common\Attributes\DescriptionAttribute;
use Chevere\Controller\Controller;
use Chevere\Parameter\Interfaces\ParametersInterface;
use Chevere\Parameter\Parameters;
use Chevere\Parameter\StringParameter;
use Chevere\Regex\Attributes\RegexAttribute;
use Chevere\Response\Interfaces\ResponseInterface;

final class ActionRunnerTestController extends Controller
{
    public function getResponseParameters(): ParametersInterface
    {
        return new Parameters(user: new StringParameter());
    }

    public function run(
        #[
        DescriptionAttribute('The username.'),
        RegexAttribute('/^[a-zA-Z]+$/')
        ]
        string $name
    ): ResponseInterface {
        return $this->getResponse(user: 'PeoplesHernandez');
    }
}
