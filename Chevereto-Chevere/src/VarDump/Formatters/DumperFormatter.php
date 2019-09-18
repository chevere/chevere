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

use const Chevere\CLI;
use Chevere\VarDump\src\Wrapper;
use Chevere\VarDump\VarDump;

/**
 * Provide Dumper VarDump representation (automatic).
 */
final class DumperFormatter
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

  public function wrap(string $key, string $dump): string
  {
    $wrapper = new Wrapper($key, $dump);
    if (CLI) {
      $wrapper->setUseCli();
    }

    return $wrapper->toString();
  }

  public static function out($var, int $indent = 0, int $depth = 0): string
  {
    $that = new VarDump(new static());
    $that->dump($var, $indent, $depth);
    return $that->toString();
  }
}
