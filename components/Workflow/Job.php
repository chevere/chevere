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

namespace Chevere\Components\Workflow;

use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Interfaces\Workflow\JobInterface;

final class Job implements JobInterface
{
    private string $name;

    public function __construct(string $name)
    {
        if (!preg_match(self::REGEX_KEY, $name)) {
            throw new InvalidArgumentException(
                (new Message('Name %name% must match %regex%'))
                    ->code('%name%', $name)
                    ->code('%regex%', self::REGEX_KEY)
            );
        }
        $this->name = $name;
    }

    public function toString(): string
    {
        return $this->name;
    }
}
