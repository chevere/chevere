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

namespace Chevere\Components\Workflow;

use Chevere\Components\DataStructures\Traits\MapTrait;
use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Core\OverflowException;
use Chevere\Interfaces\Workflow\StepInterface;
use Chevere\Interfaces\Workflow\StepsInterface;
use Chevere\Interfaces\Workflow\TaskInterface;

final class Steps implements StepsInterface
{
    use MapTrait;

    /**
     * @throws InvalidArgumentException
     * @throws OverflowException
     */
    public function withAdded(StepInterface $step, TaskInterface $task): StepsInterface
    {
        $step = $step->toString();
        if ($this->map->hasKey($step)) {
            throw new OverflowException(
                (new Message('Step %step% already added'))
                    ->code('%step', $step)
            );
        }
        $new = clone $this;
        $new->map->put($step, $task);

        return $new;
    }
}
