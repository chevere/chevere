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

use Chevere\Components\Message\Message;
use InvalidArgumentException;

final class HookAnchor
{
    const REGEX_PATTERN_ANCHOR = '/^\w+:\w+$/';

    private string $string;

    public function __construct(string $string)
    {
        $this->string = $string;
        $this->assertFormat();
    }

    public function toString(): string
    {
        return $this->string;
    }

    private function assertFormat(): void
    {
        if (preg_match(self::REGEX_PATTERN_ANCHOR, $this->string) === 0) {
            throw new InvalidArgumentException(
                (new Message("Anchor provided %provided% doesn't match regex %regex%"))
                    ->code('%provided%', $this->string)
                    ->code('%regex%', self::REGEX_PATTERN_ANCHOR)
                    ->toString()
            );
        }
    }
}
