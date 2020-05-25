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

namespace Chevere\Interfaces\VarExportable;

interface VarExportableInterface
{
    /**
     * @param mixed $var
     */
    public function __construct($var);

    /**
     * @return mixed Provides access to $var
     */
    public function var();

    /**
     * @return string var_export($var, true)
     */
    public function toExport();

    /**
     * @return string serialize($var)
     */
    public function toSerialize(): string;
}
