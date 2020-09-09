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

use Chevere\Components\Controller\Controller;
use Chevere\Components\Parameter\Parameters;
use Chevere\Components\Response\ResponseSuccess;
use Chevere\Components\Service\ServiceProviders;
use Chevere\Interfaces\Parameter\ArgumentsInterface;
use Chevere\Interfaces\Parameter\ParametersInterface;
use Chevere\Interfaces\Response\ResponseInterface;
use Chevere\Interfaces\Service\ServiceableInterface;
use Chevere\Interfaces\Service\ServiceInterface;
use Chevere\Interfaces\Service\ServiceProvidersInterface;
use PHPUnit\Framework\TestCase;

final class ServiceableTest extends TestCase
{
    public function testController(): void
    {
        $this->expectNotToPerformAssertions();
        $testController = new ServiceableTestController;
        $serviceProviders = $testController->getServiceProviders();
        foreach ($serviceProviders->getGenerator() as $methodName => $serviceName) {
            $testController = $testController->$methodName(new $serviceName);
        }
    }
}

class ServiceableTestController extends Controller implements ServiceableInterface
{
    private ServiceableTestMailer $mailer;

    public function getServiceProviders(): ServiceProvidersInterface
    {
        return (new ServiceProviders($this))
            ->withAdded('withMailer');
    }

    public function withMailer(ServiceableTestMailer $mailer): ServiceableTestController
    {
        $new = clone $this;
        $new->mailer = $mailer;

        return $new;
    }

    public function getDescription(): string
    {
        return 'greet';
    }

    public function getParameters(): ParametersInterface
    {
        return new Parameters;
    }

    public function run(ArgumentsInterface $controllerArguments): ResponseInterface
    {
        $this->mailer->send(
            'guy@chevere.com',
            'suelta el dominio tonto ql'
        );

        return new ResponseSuccess([]);
    }
}

final class ServiceableTestMailer implements ServiceInterface
{
    public function getDescription(): string
    {
        return 'Sends emails';
    }

    public function send(string $to, string $subject): void
    {
        $to;
        $subject;
        // Pretend that I send an email here...
    }
}
