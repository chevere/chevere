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

namespace Chevere\Components\Action;

use Chevere\Components\Description\Traits\DescriptionTrait;
use Chevere\Components\Message\Message;
use Chevere\Components\Parameter\Parameters;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\TypeException;
use Chevere\Interfaces\Action\ActionInterface;
use Chevere\Interfaces\Parameter\ParametersInterface;
use Chevere\Interfaces\Type\TypeInterface;

abstract class Action implements ActionInterface
{
    use DescriptionTrait;

    private string $description;

    private ParametersInterface $parameters;

    private array $returnTypes;

    /**
     * @codeCoverageIgnore
     */
    public function getParameters(): ParametersInterface
    {
        return new Parameters;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getReturnTypes(): array
    {
        return [];
    }

    final public function __construct()
    {
        $this->description = $this->getDescription();
        $this->parameters = $this->getParameters();
        $this->returnTypes = $this->getReturnTypes();
    }

    final public function description(): string
    {
        return $this->description;
    }

    final public function parameters(): ParametersInterface
    {
        return $this->parameters;
    }

    final public function returnTypes(): array
    {
        return $this->returnTypes;
    }

    final public function assertReturnTypes(array $namedArguments): void
    {
        /**
         * @var string $key
         * @var TypeInterface $type
         */
        foreach ($this->returnTypes as $key => $type) {
            if (!isset($namedArguments[$key])) {
                throw new OutOfBoundsException(
                    (new Message("Key %key% doesn't exists"))
                        ->code('%key%', $key)
                );
            }
            if (!$type->validate($namedArguments[$key])) {
                throw new TypeException(
                    (new Message("Key %key% value doesn't validate the expected type %type%"))
                    ->code('%key%', $key)
                    ->code('%type%', $type->typeHinting())
                );
            }
        }
    }
}
