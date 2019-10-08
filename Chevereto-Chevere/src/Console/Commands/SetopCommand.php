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

namespace Chevere\Console\Commands;

use Chevere\Console\Command;
use Chevere\Contracts\App\BuilderContract;

/**
 * The SetopCommand sets the option value.
 *
 * app/options
 */
final class SetopCommand extends Command
{
    const NAME = 'setop';
    const DESCRIPTION = 'Sets an option';
    const HELP = 'This command sets an option';

    const ARGUMENTS = [
        ['option', Command::ARGUMENT_REQUIRED, 'Option'],
    ];

    public function callback(BuilderContract $builder): int
    {
        $option = $this->getArgument('option');
        dd($option);
        return 0;
    }
}
