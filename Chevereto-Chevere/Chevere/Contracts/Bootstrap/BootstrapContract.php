<?php

namespace Chevere\Contracts\Bootstrap;

interface BootstrapContract
{
  public function __construct(string $documentRoot);

  public function time(): int;

  public function hrTime(): int;

  public function documentRoot(): string;

  public function rootPath(): string;

  public function appPath(): string;

  public function withCli(bool $bool): BootstrapContract;

  public function cli(): bool;

  public function withConsole(bool $bool): BootstrapContract;

  public function console(): bool;

  public function withDev(bool $bool): BootstrapContract;

  public function dev(): bool;

  public function withAppAutoloader(string $namespace): BootstrapContract;
}
