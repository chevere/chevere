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

namespace Chevere\Components\Routing\Interfaces;

use Countable;
use Ds\Set;

interface DecoratedRoutesInterface extends Countable
{
    public function __construct();

    public function withDecorated(DecoratedRouteInterface $decoratedRoute): DecoratedRoutesInterface;

    public function count(): int;

    public function contains(DecoratedRouteInterface $decoratedRoute): bool;

    public function get(int $position): DecoratedRouteInterface;
}
