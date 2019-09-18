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

namespace Chevere\VarDump\Formatters;

use Chevere\VarDump\VarDump;

/**
 * Provide plain text VarDump representation.
 */
final class PlainFormatter
{
  public function getPrefix(int $indent): string
  {
    return str_repeat(' ', $indent);
  }

  public function getEmphasis(string $string): string
  {
    return $string;
  }

  public function getEncodedChars(string $string): string
  {
    return $string;
  }

  public function wrap(string $key, string $dump): ?string
  {
    return $dump;
  }
}
