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

use Chevere\Components\Controller\Controller;
use Chevere\Components\Response\ResponseSuccess;
use Chevere\Components\Parameter\Parameters;
use Chevere\Components\Service\ServiceProviders;
use Chevere\Interfaces\Response\ResponseInterface;
use Chevere\Interfaces\Parameter\ArgumentsInterface;
use Chevere\Interfaces\Parameter\ParametersInterface;
use Chevere\Interfaces\Service\ServiceableInterface;
use Chevere\Interfaces\Service\ServiceProvidersInterface;

class TestController extends Controller implements ServiceableInterface
{
    private Mailer $mailer;

    public function getServiceProviders(): ServiceProvidersInterface
    {
        return (new ServiceProviders($this))
            ->withAdded('withMailer');
    }

    public function withMailer(Mailer $mailer): TestController
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
