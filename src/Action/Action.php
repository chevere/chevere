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
use Chevere\Action\Traits\ActionTrait;
use Chevere\Common\Traits\DescriptionTrait;
use function Chevere\Message\message;
use Chevere\Parameter\Interfaces\ParametersInterface;
use Chevere\Throwable\Exceptions\LogicException;

abstract class Action implements ActionInterface
{
    use DescriptionTrait;
    use ActionTrait;

    protected string $description;

    protected ParametersInterface $parameters;

    protected ParametersInterface $containerParameters;

    public function __construct()
    {
        $this->onConstruct();
    }

    final protected function onConstruct(): void
    {
        $this->setUpBefore();
        $this->assertRunMethod();
        $this->assertRunParameters();
        $this->parameters = $this->parameters();
        $this->containerParameters = $this->containerParameters();
        $this->setUpAfter();
    }

    /**
     * This method runs on class instantiation (before __construct).
     */
    protected function setUpBefore(): void
    {
        // enables override
    }

    /**
     * This method runs on class instantiation (after __construct).
     */
    protected function setUpAfter(): void
    {
        // enables override
    }

    // @infection-ignore-all
    protected function assertRunParameters(): void
    {
        // enables override
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
}
