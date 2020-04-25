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

use Ahc\Cli\Input\Command;

final class HttpRequestCommand extends Command
{
    public function __construct()
    {
        parent::__construct('request', 'Performs a HTTP request');

        $this
            ->argument('<method>', 'HTTP request method')
            ->argument('<uri>', 'Request URI (relative)')
            ->argument('[requester]', 'User id')
            ->option('-G --get', 'GET [json]')
            ->option('-P --post', 'POST [json]')
            ->option('-C --cookie', 'COOKIE [json]')
            ->option('-F --files', 'FILES [json]')
            ->option('-H --headers', 'HEADERS [json]')
            ->option('-B --body', 'Request body')
            ->option('-p --print', 'Print response header and body', 'boolval', false);
        // ->usage($this->writer()->colorizer()->colors(
            //     ''
            //     . '<bold>  phint init</end> <line><project></end> '
            //     . '<comment>--force --descr "Awesome project" --name "YourName" --email you@domain.com</end><eol/>'
            //     . '<bold>  phint init</end> <line><project></end> '
            //     . '<comment>--using laravel/lumen --namespace Project/Api --type project --license m</end><eol/>'
            //     . '<bold>  phint init</end> <line><project></end> '
            //     . '<comment>--php 7.0 --config /path/to/json --dev mockery/mockery --req adhocore/cli</end><eol/>'
            // ));
    }

    // When app->handle() locates `init` command it automatically calls `execute()`
    // with correct $ball and $apple values
    public function execute()
    {
        $io = $this->app()->io();

        $io->write('method ' . $this->method, true);
        $io->write('uri ' . $this->uri, true);
        $io->write('requester ' . $this->requester, true);
        $io->write('get ' . $this->get, true);

        // more codes ...

        // If you return integer from here, that will be taken as exit error code
    }
}
