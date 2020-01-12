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

namespace Chevere\Components\Console\Commands;

use Chevere\Components\Console\Command;
use Chevere\Components\App\Interfaces\BuilderContract;

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
        $option = $this->console->input()->getArgument('option');
        xdd($option);

        return 0;
    }
}
