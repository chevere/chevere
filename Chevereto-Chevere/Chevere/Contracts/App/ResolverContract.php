<?php

namespace Chevere\Contracts\App;

use Chevere\Components\App\Exceptions\ResolverException;

interface ResolverContract
{
  /**
   * @throws ResolverException if the request can't be routed
   */
  public function __construct(ResolvableContract $resolvable);

  /**
   * 
   * @return Chevere\Contracts\App\BuilderContract A resolved builder contract 
   */
  public function builder(): BuilderContract;
}
