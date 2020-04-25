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

namespace Chevere\Components\Console\Commands;

use Ahc\Cli\Input\Command;

final class ControllerRunCommand extends Command
{
    public function __construct()
    {
        parent::__construct('conrun', 'Runs a controller');

        $this
            ->argument('<FQN>', 'Controller full-qualified name')
            ->option('-a --args', 'Controller arguments [json]');
    }

    public function execute()
    {
    }
}
