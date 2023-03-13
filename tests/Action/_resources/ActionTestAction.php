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

namespace Chevere\Tests\Action\_resources;

use Chevere\Action\Action;
use function Chevere\Parameter\arrayParameter;
use Chevere\Parameter\IntegerParameter;
use Chevere\Parameter\Interfaces\ArrayTypeParameterInterface;

final class ActionTestAction extends Action
{
    public function getDescription(): string
    {
        return 'test';
    }

    public function getResponseParameter(): ArrayTypeParameterInterface
    {
        return arrayParameter(id: new IntegerParameter());
    }

    public function run(): array
    {
        return [
            'id' => 123,
        ];
    }
}
