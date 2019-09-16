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

namespace Chevere\Traits;

/**
 * Printable provides an interface for classes who may use __toString().
 *
 * @see Chevere\Contracts\Printable\Printable
 * @see Chevere\Benchmark\Benchmark
 */
trait PrintableTrait
{
    /**
     * The printable string.
     */
    private $printable;

    /**
     * Allows to cast this object as string.
     *
     * @return string
     */
    public function __toString(): string
    {
        if ($this->printable == null) {
            $this->exec();
        }

        return $this->printable ?? '';
    }

    /**
     * Print object string.
     */
    public function print(): void
    {
        if ($this->printable == null) {
            $this->exec();
        }
        echo (string) $this;
    }

    abstract public function exec(): void;
}
