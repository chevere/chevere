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
use Chevere\Components\Dependent\Dependencies;
use Chevere\Components\Message\Message;
use Chevere\Components\Parameter\Parameters;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\OverflowException;
use Chevere\Exceptions\Core\TypeException;
use Chevere\Interfaces\Action\ActionInterface;
use Chevere\Interfaces\Dependent\DependenciesInterface;
use Chevere\Interfaces\Dependent\DependentInterface;
use Chevere\Interfaces\Parameter\ParameterInterface;
use Chevere\Interfaces\Parameter\ParametersInterface;
use Chevere\Interfaces\Workflow\StepInterface;
use Chevere\Interfaces\Workflow\WorkflowInterface;
use Ds\Map as DsMap;
use Ds\Vector;
use Generator;
use function Safe\preg_match;
use Throwable;
use TypeError;

final class Workflow implements WorkflowInterface
{
    private Vector $steps;

    private ParametersInterface $parameters;

    private Map $vars;

    private DsMap $map;

    private DsMap $expected;

    private DsMap $provided;

    private DependenciesInterface $dependencies;

    public function __construct(StepInterface ...$steps)
    {
        $this->map = new DsMap();
        $this->steps = new Vector();
        $this->parameters = new Parameters();
        $this->vars = new Map();
        $this->expected = new DsMap();
        $this->provided = new DsMap();
        $this->dependencies = new Dependencies();
        $this->putAdded(...$steps);
    }

    public function count(): int
    {
        return $this->steps->count();
    }

    public function vars(): Map
    {
        return $this->vars;
    }

    public function withAdded(StepInterface ...$steps): WorkflowInterface
    {
        $new = clone $this;
        $new->putAdded(...$steps);

        return $new;
    }

    public function withAddedBefore(string $before, StepInterface ...$step): WorkflowInterface
    {
        $new = clone $this;
        $new->assertHasStepByName($before);
        foreach ($step as $name => $stepEl) {
            $new->handleStepDependencies($stepEl);
            $name = (string) $name;
            $new->putMap($name, $stepEl);
            $new->steps->insert($new->steps->find($before), $name);
        }

        return $new;
    }

    public function withAddedAfter(string $after, StepInterface ...$step): WorkflowInterface
    {
        $new = clone $this;
        $new->assertHasStepByName($after);
        foreach ($step as $name => $stepEl) {
            $new->handleStepDependencies($stepEl);
            $name = (string) $name;
            $new->putMap($name, $stepEl);
            $new->steps->insert($new->steps->find($after) + 1, $name);
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
            throw new TypeException(previous: $e);
        }
        // @codeCoverageIgnoreEnd
        catch (\OutOfBoundsException $e) {
            throw new OutOfBoundsException(
                (new Message('Step %name% not found'))
                    ->code('%name%', $step)
            );
        }
    }

    public function dependencies(): DependenciesInterface
    {
        return $this->dependencies;
    }

    public function parameters(): ParametersInterface
    {
        return $this->parameters;
    }

    public function order(): array
    {
        return $this->steps->toArray();
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
            throw new TypeException(previous: $e);
        }
        // @codeCoverageIgnoreEnd
        catch (\OutOfBoundsException $e) {
            throw new OutOfBoundsException(
                (new Message('Variable %variable% not found'))
                    ->code('%variable%', $variable)
            );
        }
    }

    public function getProvided(string $step): ParametersInterface
    {
        try {
            return $this->provided->get($step);
        }
        // @codeCoverageIgnoreStart
        catch (TypeError $e) {
            throw new TypeException(previous: $e);
        } catch (\OutOfBoundsException $e) {
            throw new OutOfBoundsException(
                (new Message('Step %step% not found'))
                    ->code('%step%', $step)
            );
        }
        // @codeCoverageIgnoreEnd
    }

    public function getGenerator(): Generator
    {
        foreach ($this->steps as $step) {
            yield $step => $this->get($step);
        }
    }

    private function putAdded(StepInterface ...$steps): void
    {
        foreach ($steps as $name => $step) {
            $this->handleStepDependencies($step);
            $name = (string) $name;
            $this->putMap($name, $step);
            $this->steps->push($name);
        }
    }

    private function handleStepDependencies(StepInterface $step): void
    {
        $actionName = $step->action();
        /** @var ActionInterface $action */
        $action = new $actionName();
        if ($action instanceof DependentInterface) {
            $this->dependencies = $this->dependencies
                ->withMerge($action->dependencies());
        }
    }

    private function putMap(string $name, StepInterface $step): void
    {
        $this->assertNoOverflow($name);
        $this->setParameters($name, $step);
        $this->map->put($name, $step);
    }

    private function assertNoOverflow(string $name): void
    {
        if ($this->map->hasKey($name)) {
            throw new OverflowException(
                (new Message('Step name %name% has been already added.'))
                    ->code('%name%', $name)
            );
        }
    }

    private function setParameters(string $name, StepInterface $step): void
    {
        $action = $step->action();
        /** @var ActionInterface $action */
        $action = new $action();
        $parameters = $action->parameters();
        $this->provided->put($name, $action->responseParameters());
        foreach ($step->arguments() as $argument => $reference) {
            $parameter = $parameters->get($argument);

            try {
                if (preg_match(self::REGEX_PARAMETER_REFERENCE, (string) $reference, $matches)) {
                    /** @var array $matches */
                    if (!$this->parameters->has($matches[1])) {
                        $this->vars = $this->vars->withPut(...[$reference => [$matches[1]]]);
                    }
                    $this->putParameter($matches[1], $parameter);
                } elseif (preg_match(self::REGEX_STEP_REFERENCE, (string) $reference, $matches)) {
                    /** @var array $matches */
                    $previousStep = (string) $matches[1];
                    $previousResponseKey = (string) $matches[2];
                    $this->assertPreviousReference($parameter, $previousStep, $previousResponseKey);
                    $expected = $this->expected->get($previousStep, []);
                    $expected[] = $previousResponseKey;
                    $this->expected->put($previousStep, $expected);
                    $this->vars = $this->vars->withPut(...[$reference => [$previousStep, $previousResponseKey]]);
                }
            } catch (Throwable $e) {
                throw new InvalidArgumentException(
                    previous: $e,
                    message: (new Message('Incompatible declaration on %name% by %action%: %message%'))
                        ->strong('%name%', $name)
                        ->strong('%action%', $action::class . ":${argument}")
                        ->strtr('%message%', '[' . $e::class . '] ' . $e->getMessage())
                );
            }
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

    private function assertMatchesExistingParameter(string $name, ParameterInterface $existent, ParameterInterface $parameter): void
    {
        if ($existent::class !== $parameter::class) {
            throw new InvalidArgumentException(
                message: (new Message('Reference %name% of type %expected% is not compatible with the type %provided% provided'))
                    ->code('%expected%', $existent::class)
                    ->strong('%name%', $name)
                    ->code('%provided%', $parameter::class)
            );
        }
    }

    private function putParameter(string $name, ParameterInterface $parameter): void
    {
        if ($this->parameters->has($name)) {
            $existent = $this->parameters->get($name);
            $this->assertMatchesExistingParameter('${' . $name . '}', $existent, $parameter);
            $this->parameters = $this->parameters
                ->withModify(...[
                    $name => $parameter,
                ]);

            return;
        }
        $this->parameters = $this->parameters
            ->withAdded(...[
                $name => $parameter,
            ]);
    }

    private function assertPreviousReference(ParameterInterface $parameter, string $previousStep, string $responseKey): void
    {
        $reference = '${' . "${previousStep}:${responseKey}" . '}';
        if (!$this->map->hasKey($previousStep)) {
            throw new OutOfBoundsException(
                (new Message("Reference %reference% not found, step %previous% doesn't exists"))
                    ->code('%reference%', $reference)
                    ->strong('%previous%', $previousStep)
            );
        }
        /** @var ParametersInterface $responseParameters */
        $responseParameters = $this->provided->get($previousStep);
        if (!$responseParameters->has($responseKey)) {
            throw new OutOfBoundsException(
                (new Message('Reference %reference% not found, response parameter %parameter% is not declared by %previous%'))
                    ->code('%reference%', $reference)
                    ->strong('%parameter%', $responseKey)
                    ->strong('%previous%', $previousStep)
            );
        }
        $this->assertMatchesExistingParameter(
            $reference,
            $responseParameters->get($responseKey),
            $parameter
        );
    }
}
