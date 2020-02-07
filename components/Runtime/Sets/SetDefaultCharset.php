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

namespace Chevere\Components\Runtime\Sets;

use Chevere\Components\Message\Message;
use Chevere\Components\Runtime\Traits\SetTrait;
use Chevere\Components\Runtime\Interfaces\SetInterface;
use InvalidArgumentException;
use RuntimeException;

/**
 * Sets the `default_charset` ini propery
 */
final class SetDefaultCharset implements SetInterface
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
        $this->assertSetDefaultCharset();
    }

    private function assertArgument(): void
    {
        $accepted = mb_list_encodings();
        if (!in_array($this->value, $accepted)) {
            throw new InvalidArgumentException(
                (new Message('Invalid value %value% provided for %className%, expecting one of the accepted encodings: %accepted%'))
                    ->code('%className%', __CLASS__)
                    ->code('%value%', $this->value)
                    ->strtr('%accepted%', '[' . implode(', ', $accepted) . ']')
                    ->toString()
            );
        }
    }

    private function assertSetDefaultCharset(): void
    {
        if (!@ini_set('default_charset', $this->value)) {
            // @codeCoverageIgnoreStart
            throw new RuntimeException(
                (new Message('Unable to set ini property %property% value %value%'))
                    ->code('%property%', 'default_charset')
                    ->code('%value%', $this->value)
                    ->toString()
            );
            // @codeCoverageIgnoreEnd
        }
    }
}
