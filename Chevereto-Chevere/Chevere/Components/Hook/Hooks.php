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

namespace Chevere\Components\Hook;

final class Hooks
{
    private object $that;

    private array $trace;

    private array $anchor;

    public function __construct(object $that, string $anchor)
    {
        $this->that = $that;
        $this->trace = null;
        $this->anchor = (new Container())
            ->getAnchor($that, $anchor);
    }

    public function withTrace(): Hooks
    {
        $new = clone $this;
        $this->trace = [];

        return $new;
    }

    public function exec(): void
    {
        if (null == $this->anchor) {
            return;
        }
        if (null !== $this->trace) {
            $this->trace['base'] = $this->that;
        }
        $this->runner();
    }

    public function hasTrace(): bool
    {
        return isset($this->trace);
    }

    public function trace(): array
    {
        return $this->trace;
    }

    private function runner(): void
    {
        foreach ($this->anchor as $entries) {
            foreach ($entries as $entry) {
                $hook = new $entry['callable'];
                $hook($this->that);
                if (null !== $this->trace) {
                    $this->trace[$entry['callable']] = $this->that;
                }
            }
        }
    }
}
