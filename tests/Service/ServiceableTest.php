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

namespace Chevere\Tests\Service;

use Chevere\Tests\Service\_resources\TestController;
use PHPUnit\Framework\TestCase;

final class ServiceableTest extends TestCase
{
    public function testController(): void
    {
        $this->expectNotToPerformAssertions();
        $testController = new TestController;
        $serviceProviders = $testController->getServiceProviders();
        foreach ($serviceProviders->getGenerator() as $methodName => $serviceName) {
            $testController = $testController->$methodName(new $serviceName);
        }
    }
}
