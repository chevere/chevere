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

use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Interfaces\Parameter\ArgumentsInterface;
use Chevere\Interfaces\Response\ResponseSuccessInterface;
use Chevere\Interfaces\Workflow\WorkflowInterface;
use Chevere\Interfaces\Workflow\WorkflowRunInterface;
use Ds\Map;
use function DeepCopy\deep_copy;

final class WorkflowRun implements WorkflowRunInterface
{
    private WorkflowInterface $workflow;

    private ArgumentsInterface $arguments;

    private Map $steps;

    public function __construct(WorkflowInterface $workflow, ArgumentsInterface $arguments)
    {
        $keys = array_keys($workflow->parameters()->toArray());
        foreach ($keys as $name) {
            // @codeCoverageIgnoreStart
            if (!$arguments->has($name)) {
                throw new InvalidArgumentException(
                    (new Message('Missing argument for %name% parameter'))
                        ->code('%name%', $name)
                );
            }
            // @codeCoverageIgnoreEnd
        }
        $this->arguments = $arguments;
        $this->workflow = $workflow;
        $this->steps = new Map;
    }

    public function __clone()
    {
        $this->steps = deep_copy($this->steps);
    }

    public function workflow(): WorkflowInterface
    {
        return $this->workflow;
    }

    public function arguments(): ArgumentsInterface
    {
        return $this->arguments;
    }

    public function withAdded(string $step, ResponseSuccessInterface $response): WorkflowRunInterface
    {
        $this->workflow->get($step);
        $expected = $this->workflow->getExpected($step);
        $missing = [];
        foreach ($expected as $name) {
            if (!isset($response->data()[$name])) {
                $missing[] = $name;
            }
        }
        if ($missing !== []) {
            throw new InvalidArgumentException(
                (new Message('Missing argument(s) %arguments%'))
                    ->code('%arguments%', implode(', ', $missing))
            );
        }
        $new = clone $this;
        $new->steps->put($step, $response->data());

        return $new;
    }

    public function has(string $name): bool
    {
        return $this->steps->hasKey($name);
    }

    public function get(string $name): array
    {
        try {
            return $this->steps->get($name);
        } catch (\OutOfBoundsException $e) {
            throw new OutOfBoundsException(
                (new Message('Task %name% not found'))
                    ->code('%name%', $name)
            );
        }
    }
}
