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

namespace Chevere\VarDump;

use Chevere\Contracts\VarDump\FormatterContract;
use Chevere\VarDump\Formatters\HtmlFormatter as DumperFormatter;
// use Chevere\VarDump\Formatters\ConsoleFormatter as DumperFormatter;
// use Chevere\VarDump\Formatters\PlainFormatter as DumperFormatter;

/**
 * A simple example in how you can extend Dumper and use your own FormatterContracr
 */
class MyDumper extends Dumper
{
  protected function getFormatter(): FormatterContract
  {
    return new DumperFormatter();
  }
}
