<?php

declare(strict_types=1);

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevere\Console\Commands;

use ReflectionMethod;
use Chevere\App\Loader;
use Chevere\HttpFoundation\Request;
use Chevere\Console\Command;

/**
 * The RequestCommand allows to pass a forged request to the App instance.
 *
 * Usage:
 * php app/console request <method> <uri>
 */
final class RequestCommand extends Command
{
    protected static $defaultName = 'request';

    protected function configure()
    {
        $this
            ->setDescription('Forge and resolve a HTTP request')
            ->setHelp('This command allows you to forge a HTTP request')
            ->addArgument('method', Command::ARGUMENT_OPTIONAL, 'HTTP request method', 'GET')
            ->addArgument('uri', Command::ARGUMENT_OPTIONAL, 'URI', '/')
            ->addArgument('parameters', Command::ARGUMENT_OPTIONAL, 'Parameters', [])
            ->addArgument('cookies', Command::ARGUMENT_OPTIONAL, 'Cookies', [])
            ->addArgument('files', Command::ARGUMENT_OPTIONAL, 'Files', [])
            ->addArgument('server', Command::ARGUMENT_OPTIONAL, 'Server', [])
            ->addArgument('content', Command::ARGUMENT_OPTIONAL, 'Content', null);
    }

    public function callback(Loader $loader): int
    {
        // Map cli arguments to Request::create
        $arguments = $this->cli->input->getArguments();
        $requestArguments = [];
        $r = new ReflectionMethod(Request::class, 'create');
        foreach ($r->getParameters() as $requestArg) {
            $requestArguments[] = $arguments[$requestArg->getName()] ?? $requestArg->getDefaultValue() ?? null;
        }
        $loader->forgeHttpRequest(...$requestArguments);
        $loader->run();

        return 1;
    }
}
