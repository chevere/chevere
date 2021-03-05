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

namespace Chevere\Tests\Controller\_resources\src;

use Chevere\Components\Parameter\StringParameter;
use Chevere\Interfaces\Parameter\ParametersInterface;
use Chevere\Interfaces\Pluggable\Plug\Hook\HookInterface;

class ControllerTestModifyParamHook implements HookInterface
{
    public function __invoke(&$argument): void
    {
        /** @var ParametersInterface $argument */
        $argument = $argument
            ->withModify(
                string: (new StringParameter())->withDefault('default')
            );
    }

    public function anchor(): string
    {
        return 'test';
    }

    public function at(): string
    {
        return ControllerTestController::class;
    }

    public function priority(): int
    {
        return 0;
    }
}
