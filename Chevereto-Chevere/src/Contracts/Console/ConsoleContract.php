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

namespace Chevere\Contracts\Console;

use Chevere\Contracts\App\BuilderContract;
use Symfony\Component\Console\Application as Symfony;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\StyleInterface;

interface ConsoleContract
{
    public function __construct();

    public static function input(): InputInterface;

    public static function output(): OutputInterface;

    public function withCommand(CommandContract $command): ConsoleContract;

    public static function command(): CommandContract;

    public static function commandString(): string;

    public static function symfony(): Symfony;

    public static function style(): StyleInterface;

    public static function inputString(): string;

    public static function isBuilding(): bool;

    public static function bind(BuilderContract $builder): bool;

    public static function run();
}
