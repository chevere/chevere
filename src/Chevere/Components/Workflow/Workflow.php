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
use Chevere\Components\Parameter\Parameters;
use Chevere\Exceptions\Core\LogicException;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\OverflowException;
use Chevere\Exceptions\Core\TypeException;
use Chevere\Interfaces\Action\ActionInterface;
use Chevere\Interfaces\Parameter\ParameterInterface;
use Chevere\Interfaces\Parameter\ParametersInterface;
use Chevere\Interfaces\Workflow\StepInterface;
use Chevere\Interfaces\Workflow\WorkflowInterface;
use Ds\Map;
use Ds\Vector;
use Generator;
use Safe\Exceptions\PcreException;
use TypeError;
use function Safe\preg_match;

final class Workflow implements WorkflowInterface
{
    private string $name;

    private Map $map;

    private Vector $steps;

    private ParametersInterface $parameters;

    private Map $vars;

    private Map $expected;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->map = new Map();
        $this->steps = new Vector();
        $this->parameters = new Parameters();
        $this->vars = new Map();
        $this->expected = new Map();
    }

    public function name(): string
    {
        return $this->name;
    }

    public function count(): int
    {
        return $this->steps->count();
    }

    public function withAdded(StepInterface ...$step): WorkflowInterface
    {
        $new = clone $this;
        foreach ($step as $stepName => $stepTask) {
            $stepName = (string) $stepName;
            $new->assertNoOverflow($stepName);
            $new->setParameters($stepName, $stepTask);
            $new->map->put($stepName, $stepTask);
            $new->steps->push($stepName);
        }

        return $new;
    }

    public function withAddedBefore(string $before, StepInterface ...$step): WorkflowInterface
    {
        $new = clone $this;
        $new->assertHasStepByName($before);
        foreach ($step as $stepName => $stepTask) {
            $stepName = (string) $stepName;
            $new->assertNoOverflow($stepName);
            $new->setParameters($stepName, $stepTask);
            $new->map->put($stepName, $stepTask);
            $new->steps->insert($new->getPosByName($before), $stepName);
        }

        return $new;
    }

    public function withAddedAfter(string $after, StepInterface ...$step): WorkflowInterface
    {
        $new = clone $this;
        $new->assertHasStepByName($after);
        foreach ($step as $stepName => $stepTask) {
            $stepName = (string) $stepName;
            $new->assertNoOverflow($stepName);
            $new->setParameters($stepName, $stepTask);
            $new->map->put($stepName, $stepTask);
            $new->steps->insert($new->getPosByName($after) + 1, $stepName);
        }

        return $new;
    }

    public function has(string $step): bool
    {
        return $this->map->hasKey($step);
    }

    /**
     * @throws TypeException
     * @throws OutOfBoundsException
     */
    public function get(string $step): StepInterface
    {
        try {
            return $this->map->get($step);
        }
        // @codeCoverageIgnoreStart
        catch (TypeError $e) {
            throw new TypeException(new Message($e->getMessage()));
        }
        // @codeCoverageIgnoreEnd
        catch (\OutOfBoundsException $e) {
            throw new OutOfBoundsException(
                (new Message('Step %name% not found'))
                    ->code('%name%', $step)
            );
        }
    }

    public function parameters(): ParametersInterface
    {
        return $this->parameters;
    }

    public function order(): array
    {
        return $this->steps->toArray();
    }

    public function hasVar(string $variable): bool
    {
        return $this->vars->hasKey($variable);
    }

    /**
     * @throws TypeException
     * @throws OutOfBoundsException
     */
    public function getVar(string $variable): array
    {
        try {
            return $this->vars->get($variable);
        }
        // @codeCoverageIgnoreStart
        catch (TypeError $e) {
            throw new TypeException(new Message($e->getMessage()));
        }
        // @codeCoverageIgnoreEnd
        catch (\OutOfBoundsException $e) {
            throw new OutOfBoundsException(
                (new Message('Variable %variable% not found'))
                    ->code('%variable%', $variable)
            );
        }
    }

    /**
     * @throws TypeException
     * @throws OutOfBoundsException
     */
    public function getExpected(string $step): array
    {
        try {
            return $this->expected->get($step);
        }
        // @codeCoverageIgnoreStart
        catch (TypeError $e) {
            throw new TypeException(new Message($e->getMessage()));
        }
        // @codeCoverageIgnoreEnd
        catch (\OutOfBoundsException $e) {
            throw new OutOfBoundsException(
                (new Message('Step %step% not found'))
                    ->code('%step%', $step)
            );
        }
    }

    public function getGenerator(): Generator
    {
        foreach ($this->steps as $step) {
            yield $step => $this->get($step);
        }
    }

    private function assertNoOverflow(string $step): void
    {
        if ($this->map->hasKey($step)) {
            throw new OverflowException(
                (new Message('Step name %name% has been already added.'))
                    ->code('%name%', $step)
            );
        }
    }

    private function setParameters(string $name, StepInterface $step): void
    {
        $action = $step->action();
        /** @var ActionInterface $action */
        $action = new $action();
        $parameters = $action->parameters();
        foreach ($step->arguments() as $argument) {
            try {
                if (preg_match(self::REGEX_PARAMETER_REFERENCE, (string) $argument, $matches)) {
                    /** @var array $matches */
                    $this->vars->put($argument, [$matches[1]]);
                    $this->putParameter($matches[1], $parameters->get($matches[1]));
                } elseif (preg_match(self::REGEX_STEP_REFERENCE, (string) $argument, $matches)) {
                    /** @var array $matches */
                    $this->assertStepExists($name, $matches);
                    $expected = $this->expected->get($matches[1], []);
                    $expected[] = $matches[2];
                    $this->expected->put($matches[1], $expected);
                    $this->vars->put($argument, [$matches[1], $matches[2]]);
                }
            }
            // @codeCoverageIgnoreStart
            catch (PcreException $e) {
                throw new LogicException(
                    (new Message('Invalid regex expression provided %regex%'))
                        ->code('%regex%', self::REGEX_STEP_REFERENCE)
                );
            }
            // @codeCoverageIgnoreEnd
        }
    }

    private function assertHasStepByName(string $step): void
    {
        if (!$this->map->hasKey($step)) {
            throw new OutOfBoundsException(
                (new Message("Task name %name% doesn't exists"))
                    ->code('%name%', $step)
            );
        }
    }

    private function getPosByName(string $step): int
    {
        /** @var int */
        return $this->steps->find($step);
    }

    private function putParameter(string $name, ParameterInterface $parameter): void
    {
        if ($this->parameters->has($name)) {
            $this->parameters = $this->parameters
                ->withModify(...[$name => $parameter]);

            return;
        }
        $this->parameters = $this->parameters
            ->withAddedRequired(...[$name => $parameter]);
    }

    private function assertStepExists(string $step, array $matches): void
    {
        if (!$this->map->hasKey($matches[1])) {
            throw new OutOfBoundsException(
                (new Message("Step %step% references parameter %parameter% from previous step %prevStep% which doesn't exists"))
                    ->code('%step%', $step)
                    ->code('%parameter%', $matches[2])
                    ->code('%prevStep%', $matches[1])
            );
        }
    }
}
