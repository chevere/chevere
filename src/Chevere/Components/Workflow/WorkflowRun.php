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
use Chevere\Components\Parameter\Arguments;
use Chevere\Exceptions\Core\ArgumentCountException;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\TypeException;
use Chevere\Interfaces\Parameter\ArgumentsInterface;
use Chevere\Interfaces\Response\ResponseInterface;
use Chevere\Interfaces\Response\ResponseSuccessInterface;
use Chevere\Interfaces\Workflow\StepInterface;
use Chevere\Interfaces\Workflow\WorkflowInterface;
use Chevere\Interfaces\Workflow\WorkflowRunInterface;
use Ds\Map;
use Ramsey\Uuid\Uuid;
use TypeError;
use function Chevere\Components\Type\returnTypeExceptionMessage;

final class WorkflowRun implements WorkflowRunInterface
{
    private string $uuid;

    private WorkflowInterface $workflow;

    private ArgumentsInterface $arguments;

    private Map $steps;

    public function __construct(WorkflowInterface $workflow, array $arguments)
    {
        $this->uuid = Uuid::uuid4()->toString();
        $this->arguments = new Arguments($workflow->parameters(), $arguments);
        $this->workflow = $workflow;
        $this->steps = new Map;
    }

    public function uuid(): string
    {
        return $this->uuid;
    }

    public function workflow(): WorkflowInterface
    {
        return $this->workflow;
    }

    public function arguments(): ArgumentsInterface
    {
        return $this->arguments;
    }

    public function withAdded(StepInterface $step, ResponseSuccessInterface $response): WorkflowRunInterface
    {
        $this->workflow->get($step);
        try {
            $expected = $this->workflow->getExpected($step);
        } catch (OutOfBoundsException $e) {
            $expected = [];
        }
        $missing = [];
        foreach ($expected as $name) {
            if (!isset($response->data()[$name])) {
                $missing[] = $name;
            }
        }
        if ($missing !== []) {
            throw new ArgumentCountException(
                (new Message('Missing argument(s) %arguments%'))
                    ->code('%arguments%', implode(', ', $missing))
            );
        }
        $new = clone $this;
        $new->steps->put($step->toString(), $response);

        return $new;
    }

    public function has(StepInterface $name): bool
    {
        return $this->steps->hasKey($name->toString());
    }

    public function get(StepInterface $name): ResponseInterface
    {
        $return = null;
        try {
            /** @var ResponseInterface $return */
            $return = $this->steps->get($name->toString());

            return $return;
        }
        // @codeCoverageIgnoreStart
        catch (TypeError $e) {
            throw new TypeException(
                returnTypeExceptionMessage(ResponseInterface::class, $return)
            );
        }
        // @codeCoverageIgnoreEnd
        catch (\OutOfBoundsException $e) {
            throw new OutOfBoundsException(
                (new Message('Task %name% not found'))
                    ->code('%name%', $name->toString())
            );
        }
    }
}
