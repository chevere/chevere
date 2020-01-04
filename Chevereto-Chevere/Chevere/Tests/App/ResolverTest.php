<?php

namespace Chevere\Tests\App;

use Chevere\Components\App\App;
use Chevere\Components\App\Build;
use Chevere\Components\App\Builder;
use Chevere\Components\App\Exceptions\RouterRequiredException;
use Chevere\Components\App\Resolver;
use Chevere\Components\App\Services;
use Chevere\Components\Http\Response;
use PHPUnit\Framework\TestCase;

final class ResolverTest extends TestCase
{
  public function testConstructMissingRouter(): void
  {
    $this->expectException(RouterRequiredException::class);
    new Resolver(
      new Builder(
        new Build(
          new App(
            new Services(),
            new Response()
          )
        )
      )
    );
  }
}
