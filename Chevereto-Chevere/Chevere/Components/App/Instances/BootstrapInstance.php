<?php

namespace Chevere\Components\App\Instances;

use Chevere\Contracts\Bootstrap\BootstrapContract;
use LogicException;

/**
 * A container for the application bootstrap.
 */
final class BootstrapInstance
{
  private static BootstrapContract $instance;

  public function __construct(BootstrapContract $bootstrap)
  {
    self::$instance = $bootstrap;
  }

  public static function get(): BootstrapContract
  {
    if (!isset(self::$instance)) {
      throw new LogicException('No runtime instance present');
    }

    return self::$instance;
  }
}
