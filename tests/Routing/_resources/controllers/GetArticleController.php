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

namespace Chevere\Tests\Routing\_resources\controllers;

use Chevere\Components\Controller\Controller;
use Chevere\Components\Controller\ControllerResponseSuccess;
use Chevere\Components\Parameter\Parameter;
use Chevere\Components\Parameter\Parameters;
use Chevere\Components\Regex\Regex;
use Chevere\Interfaces\Parameter\ArgumentedInterface;
use Chevere\Interfaces\Controller\ControllerResponseInterface;
use Chevere\Interfaces\Parameter\ParametersInterface;

final class GetArticleController extends Controller
{
    public function getParameters(): ParametersInterface
    {
        return (new Parameters)
            ->withAdded(
                (new Parameter('id'))
                    ->withRegex(new Regex('/^d+$/'))
            );
    }

    public function run(ArgumentedInterface $arguments): ControllerResponseInterface
    {
        return new ControllerResponseSuccess([]);
    }
}
