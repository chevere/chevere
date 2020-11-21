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

namespace Chevere\Tests\Service\Traits;

use Chevere\Components\Service\Traits\ServiceableTrait;
use Chevere\Interfaces\Message\MessageInterface;
use PHPUnit\Framework\TestCase;

final class ServiceableTraitTest extends TestCase
{
    public function testConstruct(): void
    {
        $serviceable = new class
        {
            use ServiceableTrait;
        };
        $this->assertInstanceOf(
            MessageInterface::class,
            $serviceable->getMissingServiceMessage('name')
        );
    }
}
