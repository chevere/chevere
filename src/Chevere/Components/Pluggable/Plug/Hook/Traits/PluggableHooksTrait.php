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

namespace Chevere\Components\Pluggable\Plug\Hook\Traits;

use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\LogicException;
use Chevere\Interfaces\Pluggable\Plug\Hook\HooksRunnerInterface;
use Chevere\Interfaces\Pluggable\Plug\Hook\PluggableHooksInterface;

trait PluggableHooksTrait
{
    private HooksRunnerInterface $hooksRunner;

    public function withHooksRunner(HooksRunnerInterface $hooksRunner): static
    {
        if (!($this instanceof PluggableHooksInterface)) {
            // @codeCoverageIgnoreStart
            throw new LogicException(
                (new Message("Instance doesn't implements %type%"))
                    ->code('%type%', PluggableHooksInterface::class)
            );
            // @codeCoverageIgnoreEnd
        }
        $new = clone $this;
        $new->hooksRunner = $hooksRunner;

        return $new;
    }

    public function hook(string $anchor, mixed &$argument): void
    {
        if (!isset($this->hooksRunner)) {
            return;
        }
        $this->hooksRunner->run($anchor, $argument);
    }
}
