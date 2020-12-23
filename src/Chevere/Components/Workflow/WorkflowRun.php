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

use Chevere\Components\DataStructure\Map;
use Chevere\Components\Message\Message;
use Chevere\Components\Parameter\Arguments;
use Chevere\Exceptions\Core\ArgumentCountException;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\TypeException;
use Chevere\Interfaces\Parameter\ArgumentsInterface;
use Chevere\Interfaces\Response\ResponseInterface;
use Chevere\Interfaces\Response\ResponseSuccessInterface;
use Chevere\Interfaces\Workflow\WorkflowInterface;
use Chevere\Interfaces\Workflow\WorkflowRunInterface;
use Ramsey\Uuid\Uuid;
use Throwable;
use TypeError;

final class WorkflowRun implements WorkflowRunInterface
{
    public Map $steps;

    private string $uuid;

    private WorkflowInterface $workflow;

    private ArgumentsInterface $arguments;

    public function __construct(WorkflowInterface $workflow, mixed ...$namedArguments)
    {
        $this->uuid = Uuid::uuid4()->toString();
        $this->arguments = new Arguments($workflow->parameters(), ...$namedArguments);
        $this->workflow = $workflow;
        $this->steps = new Map();
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

    public function withStepResponse(string $step, ResponseSuccessInterface $response): WorkflowRunInterface
    {
        $new = clone $this;
        $new->workflow->get($step);

        try {
            $expected = $new->workflow->getExpected($step);
        } catch (OutOfBoundsException $e) {
            $expected = [];
        }

        $missing = [];
        foreach ($expected as $expectParamName) {
            if (! isset($response->data()[$expectParamName])) {
                $missing[] = $expectParamName;
            }
        }
        if ($missing !== []) {
            throw new ArgumentCountException(
                (new Message('Missing argument(s) %arguments%'))
                    ->code('%arguments%', implode(', ', $missing))
            );
        }

        $new->steps = $new->steps->withPut($step, $response);

        return $new;
    }

    public function has(string $name): bool
    {
        try {
            $this->steps->assertHasKey($name);

            return true;
        } catch (Throwable $e) {
            return false;
        }
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
            throw new TypeException(new Message($e->getMessage()));
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
