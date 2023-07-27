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

namespace Chevere\String;

use Chevere\String\Interfaces\StringAssertInterface;
use Chevere\String\Interfaces\StringModifyInterface;
use Chevere\String\Interfaces\StringObjectInterface;
use Chevere\String\Interfaces\StringValidateInterface;

final class StringObject implements StringObjectInterface
{
    private StringAssertInterface $assert;

    private StringModifyInterface $modify;

    private StringValidateInterface $validate;

    public function __construct(
        public readonly string $string
    ) {
    }

    public function __toString(): string
    {
        return $this->string;
    }

    public function assert(): StringAssertInterface
    {
        return $this->assert
            ??= new StringAssert($this->string);
    }

    public function modify(): StringModifyInterface
    {
        return $this->modify
            ??= new StringModify($this->string);
    }

    public function validate(): StringValidateInterface
    {
        return $this->validate
            ??= new StringValidate($this->string);
    }
}
