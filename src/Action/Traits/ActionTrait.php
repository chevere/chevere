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

namespace Chevere\Action\Traits;

use Chevere\Action\Attributes\Strict;
use Chevere\Parameter\Interfaces\ArrayTypeParameterInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;
use Chevere\Response\Interfaces\ResponseInterface;
use Chevere\Response\Response;
use ReflectionClass;
use function Chevere\Attribute\getAttribute;
use function Chevere\Parameter\arguments;
use function Chevere\Parameter\arrayp;
use function Chevere\Parameter\assertArgument;
use function Chevere\Parameter\methodParameters;

/**
 * @method array<string, mixed> run()
 */
trait ActionTrait
{
    protected ?ParametersInterface $parameters;

    public static function acceptResponse(): ArrayTypeParameterInterface
    {
        return arrayp();
    }

    final public function getResponse(mixed ...$argument): ResponseInterface
    {
        $arguments = arguments($this->getParameters(), $argument)->toArray();
        $data = $this->run(...$arguments);
        $reflection = new ReflectionClass(static::class);
        /** @var Strict $strict */
        $strict = getAttribute($reflection, Strict::class);
        if ($strict->value) {
            /** @var array<string, mixed> $data */
            $data = assertArgument(static::acceptResponse(), $data);
        }

        return new Response(...$data);
    }

    protected function getParameters(): ParametersInterface
    {
        return $this->parameters ??= methodParameters(static::class, 'run');
    }
}
