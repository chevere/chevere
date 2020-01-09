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

namespace Chevere\Components\Runtime\Sets;

use Chevere\Components\Message\Message;
use Chevere\Components\Runtime\Traits\SetTrait;
use Chevere\Components\Runtime\Contracts\SetContract;
use Chevere\Components\Runtime\Exceptions\InvalidArgumentException;
use Chevere\Components\Runtime\Exceptions\RuntimeException;

class SetDefaultCharset implements SetContract
{
    use SetTrait;

    /**
     * Sets the default charset (ini_set)
     *
     * @param string $value Charset.
     * @throws RuntimeException If ini_set for default_charset fails.
     */
    public function __construct(string $value)
    {
        $this->value = $value;
        $this->assertArgument();
        $this->assertIniSet();
    }

    private function assertArgument(): void
    {
        $accepted = mb_list_encodings();
        if (!in_array($this->value, $accepted)) {
            throw new InvalidArgumentException(
                (new Message('Invalid value %value% provided, expecting one of the accepted encodings: %accepted%'))
                    ->code('%value%', $this->value)
                    ->code('%accepted%', implode(', ', $accepted))
                    ->toString()
            );
        }
    }

    private function assertIniSet(): void
    {
        if (!@ini_set('default_charset', $this->value)) {
            throw new RuntimeException(
                (new Message('Unable to set %s=%v'))
                    ->code('%s', 'default_charset')
                    ->code('%v', $this->value)
                    ->toString()
            );
        }
    }
}
