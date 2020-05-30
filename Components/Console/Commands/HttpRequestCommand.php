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
    }

    public function execute()
    {
        $io = $this->app()->io();

        $io->write('method ' . $this->method, true);
        $io->write('uri ' . $this->uri, true);
        $io->write('requester ' . $this->requester, true);
        $io->write('get ' . $this->get, true);
    }
}
