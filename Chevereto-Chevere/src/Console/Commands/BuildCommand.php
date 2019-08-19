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

use ReflectionMethod;
use Chevere\HttpFoundation\Request;
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
    const HELP = 'This command builds the App creating its Router and API';

    const ARGUMENTS = [];


    public function callback(LoaderContract $loader): int
    {
        $this->cli->out->block('App built', 'SUCCESS', 'fg=black;bg=green', ' ', true);
        $checksums = [];
        foreach ($loader->cacheChecksums() as $name => $keys) {
            foreach ($keys as $key => $array) {
                $checksums[] = [$name, $key, $array['path'], substr($array['checksum'], 0, 8)];
            }
        }
        $this->cli->out->table(['Cache', 'Key', 'Path', 'Checksum'], $checksums);
        return 1;
    }
}
