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

namespace Chevere\Action;

use Chevere\Action\Interfaces\ActionInterface;
use Chevere\Parameter\Cast;
use Chevere\Parameter\Interfaces\CastInterface;
use Chevere\Parameter\Interfaces\ParameterInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;
use Chevere\Throwable\Exceptions\LogicException;
use function Chevere\Message\message;
use function Chevere\Parameter\arguments;
use function Chevere\Parameter\arrayp;
use function Chevere\Parameter\assertArgument;

/**
 * @method mixed run()
 */
abstract class Action implements ActionInterface
{
    protected ?ParametersInterface $parameters = null;

    public static function acceptResponse(): ParameterInterface
    {
        return arrayp();
    }

    final public function getResponse(mixed ...$argument): CastInterface
    {
        $this->assertRunMethod();
        $this->assertRunParameters();
        $arguments = arguments($this->parameters(), $argument)->toArray();
        $run = $this->run(...$arguments);
        assertArgument(static::acceptResponse(), $run);

        return new Cast($run);
    }

    final protected function assertRunMethod(): void
    {
        if (! method_exists($this, 'run')) {
            throw new LogicException(
                message('Action %action% does not define a run method')
                    ->withCode('%action%', $this::class)
            );
        }
    }

    /**
     * @codeCoverageIgnore
     */
    protected function assertRunParameters(): void
    {
        // enables override
    }

    final protected function parameters(): ParametersInterface
    {
        if ($this->parameters === null) {
            $this->parameters = getParameters(static::class);
        }

        return $this->parameters;
    }
}
