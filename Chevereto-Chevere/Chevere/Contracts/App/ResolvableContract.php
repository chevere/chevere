<?php

namespace Chevere\Contracts\App;

use Chevere\Components\App\Exceptions\RouterRequiredException;
use Chevere\Components\App\Exceptions\RouterCantResolveException;

interface ResolvableContract
{
  /**
   * @throws RouterRequiredException if $builder lacks of a RouterContract (build->app->services->router)
   * @throws RouterCantResolveException if $builder RouterContract lacks of routing
   * 
   */
  public function __construct(BuilderContract $builder);

  /**
   * 
   * @return Chevere\Contracts\App\BuilderContract A resolvable BuilderContract
   */
  public function builder(): BuilderContract;
}
