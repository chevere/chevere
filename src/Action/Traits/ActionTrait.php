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

use Chevere\Parameter\Interfaces\ArrayTypeParameterInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;
use Chevere\Response\Interfaces\ResponseInterface;
use Chevere\Response\Response;
use function Chevere\Parameter\arguments;
use function Chevere\Parameter\arrayp;
use function Chevere\Parameter\assertArgument;
use function Chevere\Parameter\methodParameters;

/**
 * @method array<string, mixed> run()
 */
trait ActionTrait
{
    public static function description(): string
    {
        return '';
    }

    public static function isStrict(): bool
    {
        return true;
    }

    public static function acceptResponse(): ArrayTypeParameterInterface
    {
        return arrayp();
    }

    final public function getResponse(mixed ...$argument): ResponseInterface
    {
        $arguments = arguments(static::getParameters(), $argument)->toArray();
        $data = $this->run(...$arguments);
        if (static::isStrict()) {
            /** @var array<string, mixed> $data */
            $data = assertArgument(static::acceptResponse(), $data);
        }

        return new Response(...$data);
    }

    final public static function getParameters(): ParametersInterface
    {
        return methodParameters(static::class, 'run');
    }
}
