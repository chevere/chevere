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

use Chevere\Components\Message\Message;
use Chevere\Components\Regex\Exceptions\RegexException;
use Chevere\Components\Regex\Regex;
use Chevere\Components\Router\Exceptions\RouterPropertyException;
use Chevere\Components\Router\Properties\Traits\ToStringTrait;
use Chevere\Contracts\Router\Properties\RegexPropertyContract;
use TypeError;

final class RegexProperty implements RegexPropertyContract
{
    use ToStringTrait;

    /**
     * {@inheritdoc}
     */
    public function __construct(string $regex)
    {
        try {
            $this->assertStringNotEmpty($regex);
            $this->value = $regex;
            $this->assertRegex();
            $this->assertFormat();
        } catch (TypeError $e) {
            throw new RouterPropertyException(
                $e->getMessage(),
                $e->getCode(),
                $e->getPrevious()
            );
        }
    }

    private function assertRegex(): void
    {
        try {
            new Regex($this->value);
        } catch (RegexException $e) {
            throw new TypeError(
                $e->getMessage(),
                $e->getCode(),
                $e->getPrevious()
            );
        }
    }

    private function assertFormat(): void
    {
        if (!preg_match(RegexPropertyContract::REGEX_MATCHER, $this->value)) {
            throw new TypeError(
                (new Message('Invalid regex pattern: %regex%'))
                    ->code('%regex%', $this->value)
                    ->toString()
            );
        }
    }
}
