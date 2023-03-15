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

namespace Chevere\Parameter;

use Chevere\DataStructure\Map;
use Chevere\DataStructure\Traits\MapTrait;
use function Chevere\Message\message;
use Chevere\Parameter\Interfaces\GenericParameterInterface;
use Chevere\Parameter\Interfaces\GenericsInterface;
use Chevere\Parameter\Interfaces\ParameterInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;
use Chevere\Parameter\Traits\ParametersGetTypedTrait;
use Chevere\Parameter\Traits\ParametersTrait;
use Chevere\Throwable\Errors\ArgumentCountError;
use Chevere\Throwable\Errors\TypeError;

final class Generics implements GenericsInterface
{
    /**
     * @template-use MapTrait<ParameterInterface>
     */
    use MapTrait;

    use ParametersGetTypedTrait;
    use ParametersTrait;

    public function __construct(
        private GenericParameterInterface $parameter
    ) {
        $this->reset();
        $this->putAdded(
            'required',
            [
                self::GENERIC_NAME => $parameter,
            ]
        );
    }

    public function withAddedRequired(ParameterInterface ...$parameter): ParametersInterface
    {
        $new = clone $this;
        $new->assertParameterArgument(...$parameter);
        $new->reset();
        $new->putAdded(
            'required',
            [
                self::GENERIC_NAME => $new->parameter,
            ]
        );

        return $new;
    }

    public function withAddedOptional(ParameterInterface ...$parameter): ParametersInterface
    {
        $new = clone $this;
        $new->assertParameterArgument(...$parameter);
        $new->reset();
        $new->putAdded('optional', [
            self::GENERIC_NAME => $new->parameter,
        ]);

        return $new;
    }

    public function parameter(): GenericParameterInterface
    {
        return $this->parameter;
    }

    private function assertParameterArgument(ParameterInterface ...$parameter): void
    {
        $count = count($parameter);
        if ($count > 1) {
            throw new ArgumentCountError(
                message('Only one (1) parameter is allowed, %provided% provided')
                    ->withTranslate('%provided%', strval($count))
            );
        }
        foreach ($parameter as $item) {
            if (! $item instanceof GenericParameterInterface) {
                throw new TypeError(
                    message('Only %type% type is allowed, %provided% provided')
                        ->withCode('%type%', GenericParameterInterface::class)
                        ->withCode('%provided%', $item::class)
                );
            }
            $this->parameter = $item;
        }
    }

    private function reset(): void
    {
        $this->map = new Map();
        $this->required = [];
        $this->optional = [];
    }
}
