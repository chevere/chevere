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

namespace Chevere\Components\Controller\Interfaces;

use Chevere\Components\Regex\Interfaces\RegexInterface;

interface ControllerParameterInterface
{
    public function withDescription(string $string): ControllerParameterInterface;

    public function withIsRequired(bool $bool): ControllerParameterInterface;

    public function isRequired(): bool;

    public function name(): string;

    public function regex(): RegexInterface;

    public function description(): string;
}
