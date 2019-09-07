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

use InvalidArgumentException;
use JsonException;
use ReflectionMethod;
use Chevere\Http\Request;
use Chevere\Console\Command;
use Chevere\Contracts\App\LoaderContract;
use Chevere\Message;
use Chevere\Http\Response;
use Chevere\Router\Exception\RouteNotFoundException;

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
        [
            'parameters',
            'p',
            Command::OPTION_OPTIONAL,
            'Parameters [json]',
            []
        ],
        [
            'cookies',
            'c',
            Command::OPTION_OPTIONAL,
            '$_COOKIE [json]',
            []
        ],
        [
            'files',
            'f',
            Command::OPTION_OPTIONAL,
            '$_FILES [json]',
            []
        ],
        [
            'server',
            's',
            Command::OPTION_OPTIONAL,
            '$_SERVER [json]',
            []
        ],
        [
            'content',
            null,
            Command::OPTION_OPTIONAL,
            'Raw body data',
            null
        ],
        [
            'headers',
            'H',
            Command::OPTION_NONE,
            'Output headers',
        ],
        [
            'body',
            'B',
            Command::OPTION_NONE,
            'Output body',
        ],
        [
            'noformat',
            'x',
            Command::OPTION_NONE,
            'No output decorations',
        ],
    ];

    /** @var array */
    private $arguments;

    /** @var array */
    private $options;

    /** @var array */
    private $jsonOptions;

    // List of arguments which are passed as JSON
    const JSON_OPTIONS = ['parameters', 'cookies', 'files', 'server'];

    public function callback(LoaderContract $loader): int
    {
        $this->arguments = $this->cli()->input()->getArguments();
        $this->options = (array) $this->cli()->input()->getOptions();
        $this->setJsonOptions();

        $passedArguments = array_merge($this->options, $this->jsonOptions, $this->arguments);
        $requestFnArguments = [];
        $r = new ReflectionMethod(Request::class, 'create');
        foreach ($r->getParameters() as $requestArg) {
            $name = $requestArg->getName();
            $requestFnArguments[] = $passedArguments[$name] ?? $requestArg->getDefaultValue() ?? null;
        }
        $loader->setRequest(Request::create(...$requestFnArguments));

        try {
            $loader->run();
        } catch (RouteNotFoundException $e) {
            // $e Shhhh... This is just to capture the CLI output
        }

        $response = $loader->app->response();
        $this->render($response);

        return 0;
    }

    public function render(Response $response)
    {
        $isNoFormat = (bool) $this->getOption('noformat');
        $isHeaders = (bool) $this->getOption('headers');
        $isBody = (bool) $this->getOption('body');
        if (!$isHeaders && !$isBody) {
            $isHeaders = true;
            $isBody = true;
        }
        $status = $response->chvStatus();
        $headers = $response->chvHeaders();
        if (!$isNoFormat) {
            $status = '<fg=magenta>' . $status . '</>';
            $headers = '<fg=yellow>' . $headers . '</>';
        }
        $this->cli()->style()->writeln($status);
        if ($isHeaders) {
            $this->cli()->style()->writeln($headers);
        }
        if ($isBody) {
            $this->cli()->style()->write($response->chvBuffer() . "\r\n");
        }
        die(0);
    }

    private function setJsonOptions(): void
    {
        $this->jsonOptions = [];
        foreach (static::JSON_OPTIONS as $v) {
            if (is_string($this->options[$v])) {
                try {
                    $json = json_decode($this->options[$v], true, 512, JSON_THROW_ON_ERROR);
                } catch (JsonException $e) {
                    throw new InvalidArgumentException(
                        (new Message('Unable to parse %o option %s as JSON (%m).'))
                            ->code('%o', $v)
                            ->code('%s', $this->options[$v])
                            ->strtr('%m', $e->getMessage())
                            ->toString()
                    );
                }
            } else {
                $json = $this->options[$v];
            }
            $this->jsonOptions[$v] = $json;
        }
    }
}
