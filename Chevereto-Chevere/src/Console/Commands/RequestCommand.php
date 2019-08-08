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
use Chevere\HttpFoundation\Request;
use Chevere\Console\Command;
use Chevere\Contracts\App\LoaderContract;

/**
 * The RequestCommand allows to pass a forged request to the App instance.
 *
 * Usage:
 * php app/console request <method> <path>
 */
final class RequestCommand extends Command
{
    const NAME = 'request';
    const DESCRIPTION = 'Forge and resolve a HTTP request';
    const HELP = 'This command allows you to forge a HTTP request';

    const ARGUMENTS = [
        ['method', Command::ARGUMENT_OPTIONAL, 'HTTP request method', 'GET'],
        ['path', Command::ARGUMENT_OPTIONAL, 'Path', '/'],
        ['parameters', Command::ARGUMENT_OPTIONAL, 'Parameters', []],
        ['cookies', Command::ARGUMENT_OPTIONAL, 'Cookies', []],
        ['files', Command::ARGUMENT_OPTIONAL, 'Files', []],
        ['server', Command::ARGUMENT_OPTIONAL, 'Server', []],
        ['content', Command::ARGUMENT_OPTIONAL, 'Content', null],
    ];

    /**
     * Maps Symfony\Component\HttpFoundation\Request::create arguments to this command arguments.
     */
    const HTTP_REQUEST_FN_MAP = [
        'uri' => 'path',
    ];

    public function callback(LoaderContract $loader): int
    {
        $arguments = $this->cli->input->getArguments();
        $requestArguments = [];
        $r = new ReflectionMethod(Request::class, 'create');
        foreach ($r->getParameters() as $requestArg) {
            $name = $requestArg->getName();
            $mapped = self::HTTP_REQUEST_FN_MAP[$name] ?? null;
            if ($mapped) {
                $name = $mapped;
            }
            $requestArguments[] = $arguments[$name] ?? $requestArg->getDefaultValue() ?? null;
        }
        $loader->setRequest(Request::create(...$requestArguments));
        $loader->run();

        return 1;
    }
}
