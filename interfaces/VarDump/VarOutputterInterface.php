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

namespace Chevere\Interfaces\VarDump;

use Chevere\Interfaces\Writer\WriterInterface;

interface VarOutputterInterface
{
    public function __construct(
        WriterInterface $writer,
        array $debugBacktrace,
        VarDumpFormatterInterface $formatter,
        ...$vars
    );

    public function process(VarDumpOutputterInterface $outputter): void;
}
