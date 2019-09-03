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
use Chevere\Http\Request;
use Chevere\Console\Command;
use Chevere\Contracts\App\LoaderContract;
use Chevere\Message;
use InvalidArgumentException;
use JsonException;

/**
 * The RequestCommand allows to pass a forged request to the App instance.
 *
 * Usage:
 * php app/console request <method> <path>
 *
 * Both static::COMMANDS and static::OPTIONS are intended to match Request::create parameters names.
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

    // List of arguments which are passed as JSON
    const JSON_OPTIONS = ['parameters', 'cookies', 'files', 'server'];

    public function callback(LoaderContract $loader): int
    {
        $arguments = $this->cli->input()->getArguments();
        $options = $this->cli->input()->getOptions();

        $jsonOptions = [];
        foreach (static::JSON_OPTIONS as $v) {
            if (is_string($options[$v])) {
                try {
                    $json = json_decode($options[$v], true, 512, JSON_THROW_ON_ERROR);
                } catch (JsonException $e) {
                    throw new InvalidArgumentException(
                        (new Message('Unable to parse %o option %s as JSON (%m).'))
                            ->code('%o', $v)
                            ->code('%s', $options[$v])
                            ->strtr('%m', $e->getMessage())
                            ->toString()
                    );
                }
            } else {
                $json = $options[$v];
            }
            $jsonOptions[$v] = $json;
        }

        $passedArguments = array_merge($options, $jsonOptions, $arguments);

        $requestFnArguments = [];
        $r = new ReflectionMethod(Request::class, 'create');
        foreach ($r->getParameters() as $requestArg) {
            $name = $requestArg->getName();
            $requestFnArguments[] = $passedArguments[$name] ?? $requestArg->getDefaultValue() ?? null;
        }
        $loader->setRequest(Request::create(...$requestFnArguments));
        $loader->run();

        return 1;
    }
}
