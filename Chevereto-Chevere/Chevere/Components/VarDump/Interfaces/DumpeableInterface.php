<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\VarDump\Interfaces;

interface DumpeableInterface
{
    public function __construct($var);

    public function var();

    public function type(): string;

    public function template(): string;

    public function processorName(): string;
}
