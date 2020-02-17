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

namespace Chevere\Components\Hooks;

final class Hooks
{
    private object $object;

    private array $anchor;

    private array $trace;

    public function __construct(object $object, string $anchor)
    {
        $this->object = $object;
        $this->anchor = (new Container())
            ->getAnchor($object, $anchor);
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
            $this->trace['base'] = $this->object;
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
                $hook($this->object);
                if (null !== $this->trace) {
                    $this->trace[$entry['callable']] = $this->object;
                }
            }
        }
    }
}
