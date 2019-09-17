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
 * The DestroyCommand destroys the App.
 *
 * Removes `app/build.php` and all `app/cache/*` contents.
 *
 * Usage:
 * php app/console destroy
 */
final class DestroyCommand extends Command
{
    const NAME = 'destroy';
    const DESCRIPTION = 'Destroy the App';
    const HELP = 'This command destroys the App';

    public function callback(LoaderContract $loader): int
    {
        $loader->build()->destroy();
        $title = 'App destroyed';
        $this->console()->style()->success($title);
        return 0;
    }
}
