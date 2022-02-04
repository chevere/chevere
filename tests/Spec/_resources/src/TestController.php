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

namespace Chevere\Tests\Spec\_resources\src;

use Chevere\Controller\Controller;
use Chevere\Parameter\Interfaces\ArgumentsInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;
use Chevere\Parameter\Parameters;
use Chevere\Parameter\StringParameter;
use Chevere\Regex\Regex;
use Chevere\Response\Interfaces\ResponseInterface;
use Chevere\Response\Response;

class TestController extends Controller
{
    protected array $_data;

    public function getParameters(): ParametersInterface
    {
        return new Parameters(
            name: (new StringParameter())
                ->withRegex(new Regex('/^[\w]+$/')),
            id: (new StringParameter())
                ->withRegex(new Regex('/^[0-9]+$/'))
        );
    }

    public function run(ArgumentsInterface $arguments): ResponseInterface
    {
        $response = new Response();
        $data = [
            'userName' => $arguments->get('name'),
            'userId' => $arguments->get('id'),
        ];

        return $response->withData(...$data);
    }
}
