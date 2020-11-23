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
use Chevere\Interfaces\Workflow\StepInterface;

final class Step implements StepInterface
{
    private string $name;

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(string $name)
    {
        if (!preg_match(self::REGEX_KEY, $name)) {
            throw new InvalidArgumentException(
                (new Message('Name for job %name% must match %regex%'))
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
