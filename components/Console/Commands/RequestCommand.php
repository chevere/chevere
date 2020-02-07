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

namespace Chevere\Components\Console\Commands;

use InvalidArgumentException;
use JsonException;
use Chevere\Components\App\Runner;
use Chevere\Components\Console\Command;
use Chevere\Components\Http\Method;
use Chevere\Components\Http\Response;
use Chevere\Components\Http\Request;
use Chevere\Components\Message\Message;
use Chevere\Components\App\Interfaces\BuilderInterface;
use Chevere\Components\Route\PathUri;

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
            'get',
            'g',
            Command::OPTION_OPTIONAL,
            '$_GET [json]',
            [],
        ],
        [
            'post',
            'p',
            Command::OPTION_OPTIONAL,
            '$_POST [json]',
            [],
        ],
        [
            'cookie',
            'c',
            Command::OPTION_OPTIONAL,
            '$_COOKIE [json]',
            [],
        ],
        [
            'files',
            'f',
            Command::OPTION_OPTIONAL,
            '$_FILES [json]',
            [],
        ],
        [
            'headers',
            'H',
            Command::OPTION_OPTIONAL,
            'Headers',
            [],
        ],
        [
            'body',
            'B',
            Command::OPTION_OPTIONAL,
            'Body',
            null,
        ],
        [
            'response-headers',
            'rH',
            Command::OPTION_NONE,
            'Print response headers',
        ],
        [
            'response-body',
            'rB',
            Command::OPTION_NONE,
            'Print response body',
        ],
        [
            'noformat',
            'x',
            Command::OPTION_NONE,
            'No output decorations',
        ],
    ];

    /** @var array */
    private array $arguments;

    /** @var array */
    private array $options;

    /** @var array */
    private array $parsedOptions;

    // List of arguments passed as JSON
    const JSON_OPTIONS = ['get', 'post', 'cookie', 'files'];

    public function callback(BuilderInterface $builder): int
    {
        $this->arguments = $this->console()->input()->getArguments();
        $this->options = (array) $this->console()->input()->getOptions();

        $this->setParsedOptions();

        $method = new Method($this->getArgumentString('method'));
        $pathUri = new PathUri($this->getArgumentString('uri'));

        $request = new Request(
            $method,
            $pathUri,
            $this->getOptionArray('headers'),
            isset($this->options['body']) ? $this->getOptionString('body') : null,
        );

        $request
            ->withCookieParams($this->parsedOptions['cookie'])
            ->withQueryParams($this->parsedOptions['get'])
            ->withParsedBody($this->parsedOptions['post'])
            ->withUploadedFiles(Request::normalizeFiles($this->parsedOptions['files']));

        $builder = $builder
            ->withBuild(
                $builder->build()
                    ->withApp(
                        $builder->build()->app()
                            ->withRequest($request)
                    )
            );

        $runner = (new Runner($builder))
            ->withConsoleLoop()
            ->withRun();

        $builder = $runner->builder();
        $response = $builder->build()->app()->response();
        $this->render($response);

        return 0;
    }

    public function render(Response $response)
    {
        $isNoFormat = (bool) $this->console->input()->getOption('noformat');
        $isHeaders = (bool) $this->console->input()->getOption('headers');
        $isBody = (bool) $this->console->input()->getOption('body');
        if (!$isHeaders && !$isBody) {
            $isHeaders = true;
            $isBody = true;
        }
        $statusLine = $response->statusLine();
        $headersString = $response->headersString();
        if (!$isNoFormat) {
            $statusLine = '<fg=magenta>' . $statusLine . '</>';
            $headersString = '<fg=yellow>' . $headersString . '</>';
        }
        $this->console()->style()->writeln($statusLine);
        if ($isHeaders) {
            $this->console()->style()->writeln($headersString);
        }
        if ($isBody) {
            $response->sendBody();
            $this->console()->style()->write("\r\n");
        }
        die(0);
    }

    private function setParsedOptions(): void
    {
        $this->parsedOptions = [];
        foreach (self::JSON_OPTIONS as $v) {
            if (is_string($this->options[$v])) {
                try {
                    $json = json_decode($this->options[$v], true, 512, JSON_THROW_ON_ERROR);
                } catch (JsonException $e) {
                    throw new InvalidArgumentException(
                        (new Message('Unable to parse %o option %s as JSON (%m)'))
                            ->code('%o', $v)
                            ->code('%s', $this->options[$v])
                            ->strtr('%m', $e->getMessage())
                            ->toString()
                    );
                }
            } else {
                $json = $this->options[$v];
            }
            $this->parsedOptions[$v] = $json;
        }
    }
}
