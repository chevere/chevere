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
use Chevere\Regex\Interfaces\RegexInterface;
use Chevere\Regex\Regex;

#[Attribute]
final class RegexAttribute
{
    private RegexInterface $regex;
    
    public function __construct(string $regex = '')
    {
        $this->regex = new Regex($regex);
    }

    public function regex(): RegexInterface
    {
        return $this->regex;
    }
}
