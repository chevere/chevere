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

namespace Chevere\Contracts\Console;

use Symfony\Component\Console\Input\ArgvInput;

interface CliContract
{
    public function __construct(ArgvInput $input);

    public function runner();
}
