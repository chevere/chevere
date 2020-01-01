<?php

namespace Chevere\Components\Bootstrap;

use Chevere\Contracts\Bootstrap\BootstrapContract;

final class Bootstrap implements BootstrapContract
{
  private int $time;

  /** @var int High-resolution time (nanoseconds) */
  private int $hrTime;

  /** @var string Path to the document root (html) */
  private string $documentRoot;

  /** @var string Path to the project root */
  private string $rootPath;

  /** @var string Path to the application $rootPath/app/ */
  private string $appPath;

  private bool $cli;

  private bool $console;

  private bool $dev;

  public function __construct(string $documentRoot)
  {
    $this->time = time();
    $this->hrTime = hrtime(true);
    $this->documentRoot = $documentRoot;
    $this->rootPath = rtrim(str_replace('\\', '/', $this->documentRoot), '/') . '/';
    $this->appPath = $this->rootPath . 'app/';
    $this->cli = false;
    $this->console = false;
    $this->dev = false;
  }

  public function time(): int
  {
    return $this->time;
  }

  public function hrTime(): int
  {
    return $this->hrTime;
  }

  public function documentRoot(): string
  {
    return $this->documentRoot;
  }

  public function rootPath(): string
  {
    return $this->rootPath;
  }

  public function appPath(): string
  {
    return $this->appPath;
  }

  public function withCli(bool $bool): BootstrapContract
  {
    $new = clone $this;
    $new->cli = $bool;

    return $new;
  }

  public function cli(): bool
  {
    return $this->cli;
  }

  public function withConsole(bool $bool): BootstrapContract
  {
    $new = clone $this;
    $new->console = $bool;

    return $new;
  }

  public function console(): bool
  {
    return $this->console;
  }

  public function withDev(bool $bool): BootstrapContract
  {
    $new = clone $this;
    $new->dev = $bool;

    return $new;
  }

  public function dev(): bool
  {
    return $this->dev;
  }
}
