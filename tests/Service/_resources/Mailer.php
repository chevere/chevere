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

namespace Chevere\Tests\Service\_resources;

use Chevere\Interfaces\Service\ServiceInterface;

final class Mailer implements ServiceInterface
{
    public function getDescription(): string
    {
        return 'Sends emails';
    }

    public function send(string $to, string $subject): void
    {
        // Pretend that I send an email here...
    }
}
