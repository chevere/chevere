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

namespace Chevere\Attribute;

use Attribute;
use Chevere\Parameter\Interfaces\StringParameterInterface;
use Chevere\Regex\Interfaces\RegexInterface;
use Chevere\Regex\Regex;

#[Attribute]
class StringAttribute
{
    private RegexInterface $regex;

    public function __construct(
        string $regex = StringParameterInterface::REGEX_DEFAULT,
        private string $description = '',
    ) {
        $this->regex = new Regex($regex);
    }

    public function description(): string
    {
        return $this->description;
    }

    public function regex(): RegexInterface
    {
        return $this->regex;
    }
}
