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

namespace Chevere\Components\Runtime\Traits;

use function ChevereFn\stringReplaceFirst;

trait SetTrait
{
    protected string $value;

    /**
     * {@inheritdoc}
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        $explode = explode('\\', get_class($this));
        $name = stringReplaceFirst('Set', '', end($explode));

        return lcfirst($name);
    }
}
