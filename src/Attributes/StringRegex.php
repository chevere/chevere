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

namespace Chevere\Attributes;

use Attribute;
use Chevere\Parameter\Interfaces\StringParameterInterface;
use Chevere\Regex\Interfaces\RegexInterface;
use Chevere\Regex\Regex;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER | Attribute::TARGET_CLASS_CONSTANT)]
class StringRegex
{
    private RegexInterface $regex;

    public function __construct(
        string $regex = StringParameterInterface::REGEX_DEFAULT,
    ) {
        $this->regex = new Regex($regex);
    }

    public function regex(): RegexInterface
    {
        return $this->regex;
    }
}
