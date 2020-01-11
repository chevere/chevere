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

namespace Chevere\Components\VarDump\Wrappers;

use Chevere\Components\Message\Message;
use Chevere\Components\VarDump\Contracts\WrapperContract;
use InvalidArgumentException;

abstract class AbstractWrapper implements WrapperContract
{
    protected string $key;

    protected array $pallete;

    protected function assertKey(): void
    {
        if (!array_key_exists($this->key, $this->pallete())) {
            throw new InvalidArgumentException(
                (new Message('Invalid key %keyName%, expecting one of the following keys: %keys%'))
                    ->code('%keyName%', $this->key)
                    ->code('%keys%', implode(', ', array_keys($this->pallete())))
                    ->toString()
            );
        }
    }

    public function pallete(): array
    {
        return $this->pallete;
    }
}
