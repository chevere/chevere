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

namespace Chevere\Components\Runtime\Traits;

use Chevere\Contracts\Data\DataContract;

use function ChevereFn\stringReplaceFirst;

trait Set
{
    private ?string $value;

    private DataContract $data;

    public function __construct(string $value = null)
    {
        $this->value = $value;
        $this->set();
    }

    public function value(): ?string
    {
        return $this->value;
    }

    public function name(): string
    {
        $explode = explode('\\', __CLASS__);
        $name = stringReplaceFirst('Set', '', end($explode));

        return lcfirst($name);
    }

    abstract public function set(): void;
}
