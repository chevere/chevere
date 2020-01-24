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

namespace Chevere\Components\Runtime\Interfaces;

use Chevere\Components\Data\Interfaces\DataInterface;
use Chevere\Components\Data\Interfaces\DataMethodInterface;

interface RuntimeInterface extends DataMethodInterface
{
    public function __construct(SetInterface ...$set);

    /**
     * Provides access to the DataInterface instance.
     *
     * @return DataInterface A data contract with set names as keys.
     */
    public function data(): DataInterface;
}
