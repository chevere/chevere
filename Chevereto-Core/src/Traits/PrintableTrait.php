<?php

declare(strict_types=1);
/*
 * This file is part of Chevereto\Core.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevereto\Core\Traits;

/**
 * Printable provides an interface for classes who may use __toString().
 *
 * @see Interfaces\PrintableInterface
 * @see Utils\Benchmark
 * @see JSON
 */
trait PrintableTrait
{
    /**
     * The printable string.
     */
    protected $printable;

    /**
     * Allows to cast this object as string.
     *
     * @return string printable
     */
    public function __toString(): ?string
    {
        if ($this->printable == null) {
            $this->exec();
        }

        return $this->printable ?? '';
    }

    /**
     * Print object string.
     */
    public function print()
    {
        if ($this->printable == null) {
            $this->exec();
        }
        echo (string) $this; // invokes __toString, such trucazo.
    }
}
