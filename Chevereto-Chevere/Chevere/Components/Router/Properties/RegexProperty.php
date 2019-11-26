<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\Router\Properties;

use Chevere\Components\Regex\Exceptions\RegexException;
use Chevere\Components\Regex\Regex;
use Chevere\Components\Router\Exceptions\RouterPropertyException;
use Chevere\Components\Router\Properties\Traits\ToStringTrait;
use Chevere\Contracts\Router\Properties\RegexPropertyContract;

final class RegexProperty implements RegexPropertyContract
{
    use ToStringTrait;

    public function __construct(string $regex)
    {
        $this->value = $regex;
    }

    /**
     * @throws RouterPropertyException if the value doesn't match the property format
     */
    public function assert(): void
    {
        try {
            new Regex($this->value);
        } catch (RegexException $e) {
            throw new RouterPropertyException(
                $e->getMessage(),
                $e->getCode(),
                $e->getPrevious()
            );
        }
        dd($this->value);
    }
}
