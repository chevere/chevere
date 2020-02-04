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

namespace Chevere\Components\Router\Properties;

use InvalidArgumentException;
use Chevere\Components\Message\Message;
use Chevere\Components\Regex\Regex;
use Chevere\Components\Router\Properties\Traits\ToStringTrait;
use Chevere\Components\Router\Interfaces\Properties\RegexPropertyInterface;

final class RegexProperty extends AbstractProperty implements RegexPropertyInterface
{
    use ToStringTrait;

    /**
     * Creates a new instance.
     *
     * @param string $regex Regex pattern used when resolving routing
     *
     * @throws RouterPropertyException if the value doesn't match the property format
     */
    public function __construct(string $regex)
    {
        $this->value = $regex;
        $this->tryAsserts();
    }

    protected function asserts(): void
    {
        $this->assertStringNotEmpty($this->value);
        $this->assertRegex();
        $this->assertFormat();
    }

    private function assertRegex(): void
    {
        new Regex($this->value);
    }

    private function assertFormat(): void
    {
        if (!preg_match(RegexPropertyInterface::REGEX_MATCHER, $this->value)) {
            throw new InvalidArgumentException(
                (new Message('Invalid regex pattern: %regex%'))
                    ->code('%regex%', $this->value)
                    ->toString()
            );
        }
    }
}
