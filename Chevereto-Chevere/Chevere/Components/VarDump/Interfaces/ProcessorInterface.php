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

interface ProcessorInterface
{
    public function __construct(VarDumpInterface $varDump);

    public function info(): string;

    public function val(): string;

    public function withProcess(): ProcessorInterface;
}
