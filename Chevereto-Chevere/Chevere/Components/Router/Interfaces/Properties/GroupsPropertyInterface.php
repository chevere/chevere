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

namespace Chevere\Components\Router\Interfaces\Properties;

use Chevere\Components\Common\Interfaces\ToArrayInterface;

interface GroupsPropertyInterface extends ToArrayInterface
{
    /** @var string property name */
    const NAME = 'groups';

    public function __construct(array $groups);
}
