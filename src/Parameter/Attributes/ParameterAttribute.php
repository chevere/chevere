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

namespace Chevere\Parameter\Attributes;

use Attribute;
use Chevere\Regex\Interfaces\RegexInterface;
use Chevere\Regex\Regex;

#[Attribute]
final class ParameterAttribute
{
    private RegexInterface $regex;

    public function __construct(private string $description = '', string $regex = '/.*/')
    {
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
