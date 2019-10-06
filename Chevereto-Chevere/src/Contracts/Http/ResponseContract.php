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

namespace Chevere\Contracts\Http;

use Chevere\Http\GuzzleResponse;

interface ResponseContract
{
    public function __construct();

    public function withGuzzle(GuzzleResponse $guzzle): ResponseContract;

    public function guzzle(): GuzzleResponse;

    public function status(): string;

    public function headers(): string;

    public function content(): string;

    public function sendHeaders(): ResponseContract;

    public function sendBody(): ResponseContract;
}
