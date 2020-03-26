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

namespace Chevere\Components\Controller;

use Chevere\Components\Controller\Interfaces\ControllerParameterInterface;
use Chevere\Components\Regex\Interfaces\RegexInterface;
use Chevere\Components\Str\Exceptions\StrCtypeSpaceException;
use Chevere\Components\Str\Exceptions\StrEmptyException;
use Chevere\Components\Str\StrAssert;

final class ControllerParameter implements ControllerParameterInterface
{
    private string $name;

    private string $regex;

    /**
     * @throws StrCtypeSpaceException if $name contains ctype space
     * @throws StrEmptyException if $name is empty
     */
    public function __construct(string $name, RegexInterface $regex)
    {
        $this->name = $name;
        $this->regex = $regex->toString();
        $this->assertName();
    }

    public function name(): string
    {
        return $this->name;
    }

    public function regex(): string
    {
        return $this->regex;
    }

    private function assertName(): void
    {
        (new StrAssert($this->name))
            ->notEmpty()
            ->notCtypeSpace()
            ->notContains(' ');
    }
}
