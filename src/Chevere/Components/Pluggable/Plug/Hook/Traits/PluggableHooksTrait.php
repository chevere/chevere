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

    public function withHooksRunner(HooksRunnerInterface $hooksRunner): PluggableHooksInterface
    {
        if (! ($this instanceof PluggableHooksInterface)) {
            // @codeCoverageIgnoreStart
            throw new LogicException(
                (new Message("Instance doesn't implements %type%"))
                    ->code('%type%', PluggableHooksInterface::class)
            );
            // @codeCoverageIgnoreEnd
        }
        $new = clone $this;
        $new->hooksRunner = $hooksRunner;
        /**
         * @var PluggableHooksInterface $new
         */
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
