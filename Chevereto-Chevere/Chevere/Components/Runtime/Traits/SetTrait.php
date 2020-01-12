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

use Chevere\Components\Data\Interfaces\DataContract;
use function ChevereFn\stringReplaceFirst;

trait SetTrait
{
    private string $value;

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
        $explode = explode('\\', __CLASS__);
        $name = stringReplaceFirst('Set', '', end($explode));

        return lcfirst($name);
    }
}
