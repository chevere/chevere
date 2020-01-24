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

/**
 * Set the HTTP scheme
 */
final class SetUriScheme implements SetInterface
{
    use SetTrait;

    private array $accept = ['http', 'https'];

    /**
     * Creates a new instance.
     *
     * @param string $value Http scheme, either http or https.
     * @throws InvalidArgumentException If the value passed is not a Http scheme
     */
    public function __construct(string $value)
    {
        $this->value = $value;
        $this->assertArgument();
    }

    private function assertArgument(): void
    {
        if (!in_array($this->value, $this->accept)) {
            throw new InvalidArgumentException(
                (new Message('Expecting %expecting%, %value% provided'))
                    ->code('%expecting%', implode(', ', $this->accept))
                    ->code('%value%', $this->value)
                    ->toString()
            );
        }
    }
}
