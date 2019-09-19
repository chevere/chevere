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

namespace Chevere\Contracts\VarDump;

interface FormatterContract
{
  /**
   * @param int $indent Number of spaces to prefix
   */
  public function getIndent(int $indent): string;

  /**
   * @param string String to emphatize
   */
  public function getEmphasis(string $string): string;

  /**
   * @param string String to encode its chars
   */
  public function getEncodedChars(string $string): string;

  public function wrap(string $key, string $dump): string;
}
