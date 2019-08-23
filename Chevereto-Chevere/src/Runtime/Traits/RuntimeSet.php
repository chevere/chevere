<?php

declare(strict_types=1);

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevere\Runtime\Traits;

use Chevere\Utility\Str;
use Chevere\Contracts\DataContract;

trait RuntimeSet
{
    /** @var string */
    private $value;

    /** @var DataContract */
    private $data;

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
        $name = Str::replaceFirst('RuntimeSet', '', end($explode));
        return lcfirst($name);
    }

    abstract public function set(): void;
}
