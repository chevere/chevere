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

namespace Chevere\Components\VarDump\Interfaces;

use Chevere\Components\Writers\Interfaces\WriterInterface;

interface OutputterInterface
{
    public function setUp(WriterInterface $writer, array $backtrace);

    public function prepare(): void;

    public function callback(): void;

    public function caller(): string;
}
