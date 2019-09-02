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
        ['uri', Command::ARGUMENT_OPTIONAL, 'URI', '/'],
    ];

    const OPTIONS = [
        ['parameters', 'p', Command::OPTION_OPTIONAL, 'Parameters [json]', []],
        ['cookies', 'c', Command::OPTION_OPTIONAL, '$_COOKIE [json]', []],
        ['files', 'f', Command::OPTION_OPTIONAL, '$_FILES [json]', []],
        ['server', 's', Command::OPTION_OPTIONAL, '$_SERVER [json]', []],
        ['content', 'r', Command::OPTION_OPTIONAL, 'Raw body data', null],
    ];

    /** If required, this will map command arguments and options to Request::create */
    const HTTP_REQUEST_FN_MAP = [
        // 'uri' => 'uri',
        // 'method' => 'method',
        // 'parameters' => 'parameters',
        // 'cookies' => 'cookies',
        // 'files' => 'files',
        // 'server' => 'server',
        // 'content' => 'content'
    ];


    // List of arguments which are passed as JSON
    const JSON_OPTIONS = ['parameters', 'cookies', 'files', 'server'];

    public function callback(LoaderContract $loader): int
    {
        $arguments = $this->cli->input()->getArguments();
        $options = $this->cli->input()->getOptions();

        $jsonOptions = [];
        foreach (static::JSON_OPTIONS as $v) {
            $jsonOptions[$v] = is_string($options[$v]) ? json_decode($options[$v], true) : $options[$v];
        }

        $options = array_merge($options, $jsonOptions);

        $requestArguments = [];
        $r = new ReflectionMethod(Request::class, 'create');
        foreach ($r->getParameters() as $requestArg) {
            $name = $requestArg->getName();
            $requestArguments[] = $arguments[$name] ?? $requestArg->getDefaultValue() ?? null;
        }
        $loader->setRequest(Request::create(...$requestArguments));
        $loader->run();

        return 1;
    }

    // private function getMappedName(string $name): string
    // {
    //     $mapped = self::HTTP_REQUEST_FN_MAP[$name] ?? null;
    //     return $mapped ?? $name;
    // }
}
