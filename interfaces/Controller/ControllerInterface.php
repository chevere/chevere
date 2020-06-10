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

namespace Chevere\Interfaces\Controller;

interface ControllerInterface
{
    public function getParameters(): ControllerParametersInterface;

    public function parameters(): ControllerParametersInterface;

    public function getDescription(): string;

    public function description(): string;

    public function setUp(): void;

    public function tearDown(): void;

    public function run(ControllerArgumentsInterface $controllerArguments): ControllerResponseInterface;
}
