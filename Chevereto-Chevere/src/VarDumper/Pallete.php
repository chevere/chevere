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

namespace Chevere\VarDumper;

abstract class Pallete
{
  /**
   * Color palette used in HTML.
   */
  const PALETTE = [
    VarDumper::TYPE_STRING => '#e67e22', // orange
    VarDumper::TYPE_FLOAT => '#f1c40f', // yellow
    VarDumper::TYPE_INTEGER => '#f1c40f', // yellow
    VarDumper::TYPE_BOOLEAN => '#9b59b6', // purple
    VarDumper::TYPE_NULL => '#7f8c8d', // grey
    VarDumper::TYPE_OBJECT => '#e74c3c', // red
    VarDumper::TYPE_ARRAY => '#2ecc71', // green
    VarDumper::_FILE => null,
    VarDumper::_CLASS => '#3498db', // blue
    VarDumper::_OPERATOR => '#7f8c8d', // grey
    VarDumper::_FUNCTION => '#9b59b6', // purple
  ];

  /**
   * Color palette used in CLI.
   */
  const CONSOLE = [
    VarDumper::TYPE_STRING => 'color_136', // yellow
    VarDumper::TYPE_FLOAT => 'color_136', // yellow
    VarDumper::TYPE_INTEGER => 'color_136', // yellow
    VarDumper::TYPE_BOOLEAN => 'color_127', // purple
    VarDumper::TYPE_NULL => 'color_245', // grey
    VarDumper::TYPE_OBJECT => 'color_167', // red
    VarDumper::TYPE_ARRAY => 'color_41', // green
    VarDumper::_FILE => null,
    VarDumper::_CLASS => 'color_147', // blue
    VarDumper::_OPERATOR => 'color_245', // grey
    VarDumper::_FUNCTION => 'color_127', // purple
  ];
}
