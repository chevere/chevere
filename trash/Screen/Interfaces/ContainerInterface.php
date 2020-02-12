<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevere.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\Screen\Interfaces;

interface ContainerInterface
{
    const RUNTIME = 'runtime';
    const DEBUG = 'debug';
    const CONSOLE = 'console';

    public function __construct(ScreenInterface $runtime);

    public function withDebugScreen(ScreenInterface $screen);

    public function withConsoleScreen(ScreenInterface $screen);

    public function withAddedScreen(string $name, ScreenInterface $screen): ContainerInterface;

    public function has(string $name): bool;

    public function get(string $name): ScreenInterface;

    public function getAll(): array;
}
