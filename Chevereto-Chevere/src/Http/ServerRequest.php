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

use Chevere\Contracts\Http\RequestContract;
use Chevere\Http\Traits\RequestTrait;
use GuzzleHttp\Psr7\CachingStream;
use GuzzleHttp\Psr7\LazyOpenStream;
use GuzzleHttp\Psr7\ServerRequest as GuzzleHttpServerRequest;

final class ServerRequest extends GuzzleHttpServerRequest implements RequestContract
{
  use RequestTrait;

  /**
   * Return a ServerRequest populated with superglobals:
   * $_GET
   * $_POST
   * $_COOKIE
   * $_FILES
   * $_SERVER
   *
   * @return ServerRequestInterface
   */
  public static function fromGlobals()
  {
    $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
    $headers = getallheaders();
    $uri = static::getUriFromGlobals();
    $body = new CachingStream(new LazyOpenStream('php://input', 'r+'));
    $protocol = isset($_SERVER['SERVER_PROTOCOL']) ? str_replace('HTTP/', '', $_SERVER['SERVER_PROTOCOL']) : '1.1';

    $serverRequest = new static($method, $uri, $headers, $body, $protocol, $_SERVER);

    return $serverRequest
      ->withCookieParams($_COOKIE)
      ->withQueryParams($_GET)
      ->withParsedBody($_POST)
      ->withUploadedFiles(static::normalizeFiles($_FILES));
  }
}
