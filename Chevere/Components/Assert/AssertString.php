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

namespace Chevere\Components\Assert;

use Chevere\Components\Assert\Exceptions\AssertStringException;
use Chevere\Components\Assert\Interfaces\AssertStringnterface;
use Chevere\Components\Message\Message;

final class AssertString implements AssertStringnterface
{
    private string $string;

    public function __construct(string $string)
    {
        $this->string = $string;
    }

    public function notEmpty(): AssertStringnterface
    {
        if ($this->string === '') {
            throw new AssertStringException(
                (new Message('String %algo% provided'))
                    ->strong('%algo%', 'empty')
                    ->toString()
            );
        }

        return $this;
    }

    public function notCtypeSpace(): AssertStringnterface
    {
        if (ctype_space($this->string) === true) {
            throw new AssertStringException(
                (new Message('String %algo% provided'))
                    ->strong('%algo%', 'ctype space')
                    ->toString()
            );
        }

        return $this;
    }
}
