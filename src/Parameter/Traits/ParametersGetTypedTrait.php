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

namespace Chevere\Parameter\Traits;

use Chevere\Parameter\Interfaces\ArrayParameterInterface;
use Chevere\Parameter\Interfaces\BooleanParameterInterface;
use Chevere\Parameter\Interfaces\FileParameterInterface;
use Chevere\Parameter\Interfaces\FloatParameterInterface;
use Chevere\Parameter\Interfaces\IntegerParameterInterface;
use Chevere\Parameter\Interfaces\ObjectParameterInterface;
use Chevere\Parameter\Interfaces\StringParameterInterface;

trait ParametersGetTypedTrait
{
    public function getArray(string $name): ArrayParameterInterface
    {
        /** @var ArrayParameterInterface */
        return $this->get($name);
    }

    public function getBoolean(string $name): BooleanParameterInterface
    {
        /** @var BooleanParameterInterface */
        return $this->get($name);
    }

    public function getFile(string $name): FileParameterInterface
    {
        /** @var FileParameterInterface */
        return $this->get($name);
    }

    public function getFloat(string $name): FloatParameterInterface
    {
        /** @var FloatParameterInterface */
        return $this->get($name);
    }

    public function getInteger(string $name): IntegerParameterInterface
    {
        /** @var IntegerParameterInterface */
        return $this->get($name);
    }

    public function getObject(string $name): ObjectParameterInterface
    {
        /** @var ObjectParameterInterface */
        return $this->get($name);
    }

    public function getString(string $name): StringParameterInterface
    {
        /** @var StringParameterInterface */
        return $this->get($name);
    }
}
