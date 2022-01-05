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
use function Chevere\Components\VarSupport\deepCopy;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\TypeException;
use Chevere\Interfaces\Parameter\ArgumentsInterface;
use Chevere\Interfaces\Response\ResponseInterface;
use Chevere\Interfaces\Workflow\WorkflowInterface;
use Chevere\Interfaces\Workflow\WorkflowRunInterface;
use Ds\Map;
use Ramsey\Uuid\Uuid;
use TypeError;

final class WorkflowRun implements WorkflowRunInterface
{
    private Map $steps;

    private string $uuid;

    private ArgumentsInterface $arguments;

    public function __construct(private WorkflowInterface $workflow, mixed ...$namedArguments)
    {
        $this->uuid = Uuid::uuid4()->toString();
        $this->arguments = new Arguments($workflow->parameters(), ...$namedArguments);
        $this->steps = new Map();
    }

    public function __clone()
    {
        $this->steps = deepCopy($this->steps);
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

    public function withStepResponse(string $step, ResponseInterface $response): WorkflowRunInterface
    {
        $new = clone $this;
        $new->workflow->steps()->get($step);
        $tryArguments = new Arguments(
            $new->workflow->getProvided($step),
            ...$response->data()
        );
        $tryArguments->parameters();
        $new->steps->put($step, $response);

        return $new;
    }

    public function has(string $name): bool
    {
        return $this->steps->hasKey($name);
    }

    /**
     * @throws TypeException
     * @throws OutOfBoundsException
     */
    public function get(string $name): ResponseInterface
    {
        try {
            return $this->steps->get($name);
        }
        // @codeCoverageIgnoreStart
        catch (TypeError $e) {
            throw new TypeException(previous: $e);
        }
        // @codeCoverageIgnoreEnd
        catch (\OutOfBoundsException $e) {
            throw new OutOfBoundsException(
                (new Message('Step %name% not found'))
                    ->code('%name%', $name)
            );
        }
    }
}
