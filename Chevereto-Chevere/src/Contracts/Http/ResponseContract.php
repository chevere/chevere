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

use Chevere\JsonApi\JsonApi;

interface ResponseContract
{
  /**
   * Set response content as JSON string with JSON headers
   */
  public function setJsonContent(JsonApi $jsonApi): void;

  /**
   * Get the HTTP status string
   * 
   * @return string The HTTP status string like `HTTP/1.1 200 OK`
   */
  public function getStatusString(): string;

  /**
   * Returns the response without body (status + headers)
   */
  public function getNoBody(): string;

  /**
   * Set JSON response headers 
   */
  public function setJsonHeaders(): void;
}
