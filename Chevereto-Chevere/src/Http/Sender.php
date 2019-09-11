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

namespace Chevere\Http;

use Chevere\Contracts\Http\ResponseContract;

final class Sender
{
  public function __construct(ResponseContract $response)
  {
    header($response->statusString(), true, $response->guzzle()->getStatusCode());
    foreach ($response->guzzle()->getHeaders() as $name => $values) {
      foreach ($values as $value) {
        header("$name: $value", false);
      }
    }
    $stream = $response->guzzle()->getBody();
    if ($stream->isSeekable()) {
      $stream->rewind();
    }
    while (!$stream->eof()) {
      echo $stream->read(1024 * 8);
    }
  }
}
