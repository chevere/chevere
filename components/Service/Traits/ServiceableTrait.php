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

namespace Chevere\Components\Service\Traits;

use Chevere\Components\Message\Message;
use Chevere\Interfaces\Message\MessageInterface;

trait ServiceableTrait
{
    public function getMissingServiceMessage(string $service): MessageInterface
    {
        return (new Message('Missing %service% service'))
            ->code('%service%', $service);
    }
}
