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

namespace Chevere\Components\Parameter;

use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Interfaces\Parameter\ParameterOptionalInterface;

final class ParameterOptional extends Parameter implements ParameterOptionalInterface
{
    private string $default = '';

    public function withDefault(string $default): ParameterOptionalInterface
    {
        if ($this->regex->match($default) == []) {
            throw new InvalidArgumentException(
                (new Message('Default value must match the parameter regex %regexString%'))
                    ->code('%regexString%', $this->regex->toString())
            );
        }
        $new = clone $this;
        $new->default = $default;

        return $new;
    }

    public function default(): string
    {
        return $this->default;
    }
}
