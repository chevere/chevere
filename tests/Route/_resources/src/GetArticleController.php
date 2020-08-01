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

namespace Chevere\Tests\Route\_resources\src;

use Chevere\Components\Controller\Controller;
use Chevere\Components\Response\ResponseSuccess;
use Chevere\Components\Parameter\Parameter;
use Chevere\Components\Parameter\Parameters;
use Chevere\Components\Regex\Regex;
use Chevere\Interfaces\Response\ResponseInterface;
use Chevere\Interfaces\Parameter\ArgumentsInterface;
use Chevere\Interfaces\Parameter\ParametersInterface;

final class GetArticleController extends Controller
{
    public function getParameters(): ParametersInterface
    {
        return (new Parameters)
            ->withAdded(
                (new Parameter('id'))
                    ->withRegex(new Regex('/^\d+$/'))
            );
    }

    public function run(ArgumentsInterface $arguments): ResponseInterface
    {
        return new ResponseSuccess([]);
    }
}
