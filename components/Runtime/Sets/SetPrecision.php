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

use RuntimeException;
use Chevere\Components\Message\Message;
use Chevere\Components\Runtime\Traits\SetTrait;
use Chevere\Components\Runtime\Interfaces\SetInterface;
use InvalidArgumentException;

/**
 * Sets the `precision` ini property
 */
final class SetPrecision implements SetInterface
{
    use SetTrait;

    /**
     * Creates a new instance.
     *
     * @param string $value the precision value to pass to `ini_set`
     * @throws RuntimeException If unable to set the value
     */
    public function __construct(string $value)
    {
        $this->value = $value;
        $this->assertArgument();
        $this->assertSetPrecision();
    }

    private function assertArgument(): void
    {
        if (!filter_var($this->value, FILTER_VALIDATE_INT)) {
            throw new InvalidArgumentException(
                (new Message('Value must be a string integer, value %value% provided'))
                    ->code('%value%', $this->value)
                    ->toString()
            );
        }
    }

    private function assertSetPrecision(): void
    {
        if (!@ini_set('precision', $this->value)) {
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
