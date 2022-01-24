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

namespace Chevere\VarDump\Interfaces;

use Chevere\Writer\Interfaces\WriterInterface;

/**
 * Describes the component in charge of writing information about a variable.
 */
interface VarOutputInterface
{
    public function __construct(
        WriterInterface $writer,
        array $trace,
        VarDumpFormatInterface $format
    );

    /**
     * Process the var output streaming.
     */
    public function process(VarDumpOutputInterface $output, ...$vars): void;
}
