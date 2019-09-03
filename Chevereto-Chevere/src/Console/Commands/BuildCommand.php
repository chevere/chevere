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
use Chevere\Contracts\App\LoaderContract;

/**
 * The BuildCommand builds the App.
 *
 * Usage:
 * php app/console build
 */
final class BuildCommand extends Command
{
    const NAME = 'build';
    const DESCRIPTION = 'Build the App';
    const HELP = 'This command builds the App';

    public function callback(LoaderContract $loader): int
    {
        $loader->build();
        $this->cli->style()->block('App built', 'SUCCESS', 'fg=black;bg=green', ' ', true);
        $checksums = [];
        foreach ($loader->cacheChecksums() as $name => $keys) {
            foreach ($keys as $key => $array) {
                $checksums[] = [$name, $key, $array['path'], substr($array['checksum'], 0, 8)];
            }
        }
        $this->cli->style()->table(['Cache', 'Key', 'Path', 'Checksum'], $checksums);
        return 0;
    }
}
