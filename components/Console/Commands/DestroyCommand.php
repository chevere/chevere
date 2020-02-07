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

use Chevere\Components\Console\Command;
use Chevere\Components\App\Interfaces\BuilderInterface;

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

    public function callback(BuilderInterface $builder): int
    {
        $builder->build()->destroy();
        $title = 'App destroyed';
        $this->console()->style()->success($title);

        return 0;
    }
}
