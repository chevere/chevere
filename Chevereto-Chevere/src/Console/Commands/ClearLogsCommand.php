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

use Chevere\App\App;
use Chevere\Console\Command;
use Chevere\Contracts\App\LoaderContract;
use Chevere\Path\Path;

/**
 * The ClearLogsCommand removes app stored logs.
 */
final class ClearLogsCommand extends Command
{
    const NAME = 'clear-logs';
    const DESCRIPTION = 'Clear app stored logs';
    const HELP = 'This command clears logs stored by the app';

    public function callback(LoaderContract $loader): int
    {
        $delete = Path::removeContents(App::PATH_LOGS);
        $count = count($delete);
        $this->console()->style()->success(
            $count > 0 ? sprintf('App logs cleared (%s files)', $count) : 'No app logs to remove'
        );
        if ($count) {
            $this->console()->style()->listing($delete);
        }

        return 0;
    }
}
