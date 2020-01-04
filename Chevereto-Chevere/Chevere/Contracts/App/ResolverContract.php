<?php

namespace Chevere\Contracts\App;

use Chevere\Components\App\Exceptions\RouterRequiredException;
use Chevere\Components\App\Exceptions\RouterCantResolveException;
use Chevere\Components\App\Exceptions\ResolverException;

interface ResolverContract
{
  /**
   * @throws RouterRequiredException if $builder lacks of a RouterContract (build->app->services->router)
   * @throws RouterCantResolveException if $builder RouterContract lacks of routing
   * @throws ResolverException if the given route is not routed
   */
  public function __construct(BuilderContract $builder);

  public function builder(): BuilderContract;
}
