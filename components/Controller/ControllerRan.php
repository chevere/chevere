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

use Chevere\Components\Controller\Interfaces\ControllerRanInterface;
use Chevere\Components\Message\Message;
use InvalidArgumentException;

final class ControllerRan implements ControllerRanInterface
{
    private int $code;

    private array $data;

    public function __construct(int $code, array $data)
    {
        $this->code = $code;
        $this->assertCode();
        $this->data = $data;
    }

    public function code(): int
    {
        return $this->code;
    }

    public function data(): array
    {
        return $this->data;
    }

    private function assertCode(): void
    {
        $range = [0, 254];
        if ($this->code < $range[0] || $this->code > $range[1]) {
            throw new InvalidArgumentException(
                (new Message('Code value %used% is not in the accepted range %range%'))
                    ->code('%used%', (string) $this->code)
                    ->code('%range%', implode('-', $range))
                    ->toString()
            );
        }
    }
}
