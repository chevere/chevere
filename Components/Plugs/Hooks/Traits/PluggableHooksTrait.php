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

namespace Chevere\Components\Plugs\Hooks\Traits;

use Chevere\Interfaces\Plugs\Hooks\HooksRunnerInterface;

trait PluggableHooksTrait
{
    private HooksRunnerInterface $hooksRunner;

    public function withHooksRunner(HooksRunnerInterface $hooksRunner): self
    {
        $new = clone $this;
        $new->hooksRunner = $hooksRunner;

        return $new;
    }

    public function hook(string $anchor, &$argument): void
    {
        if (isset($this->hooksRunner) === false) {
            return;
        }
        $this->hooksRunner->run($anchor, $argument);
    }
}
