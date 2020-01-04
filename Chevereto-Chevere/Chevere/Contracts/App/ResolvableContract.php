<?php

namespace Chevere\Contracts\App;

use Chevere\Components\App\Exceptions\RouterRequiredException;
use Chevere\Components\App\Exceptions\RouterCantResolveException;
use Chevere\Components\App\Exceptions\RequestRequiredException;

interface ResolvableContract
{
  /**
   * @throws RequestRequiredException if $builder lacks of a request 
   * @throws RouterRequiredException if $builder lacks of a RouterContract
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
