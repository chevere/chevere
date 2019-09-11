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

namespace Chevere\Http\Traits;

trait RequestTrait
{
  /**
   * Returns true if the request is a XMLHttpRequest.
   *
   * It works if your JavaScript library sets an X-Requested-With HTTP header.
   * It is known to work with common JavaScript frameworks:
   *
   * @see https://wikipedia.org/wiki/List_of_Ajax_frameworks#JavaScript
   *
   * @return bool true if the request is an XMLHttpRequest, false otherwise
   */
  public function isXmlHttpRequest(): bool
  {
    return 'XMLHttpRequest' == $this->getHeaderLine('X-Requested-With');
  }

  public function protocolString()
  {
    return sprintf('HTTP/%s', $this->getProtocolVersion());
  }
}
