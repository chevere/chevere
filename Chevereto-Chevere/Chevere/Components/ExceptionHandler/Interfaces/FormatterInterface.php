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

namespace Chevere\Components\ExceptionHandler\Interfaces;

use Chevere\Components\VarDump\Interfaces\FormatterInterface as VarDumpFormatterInterface;

interface FormatterInterface
{
    public function getVarDumpFormatter(): VarDumpFormatterInterface;

    /**
     * Returns the template used for each trace entry.
     *
     * - %cssEvenClass% Css even-class (pre--even)
     * - %i% Stack number
     * - %file% File
     * - %line% Line
     * - %fileLine% File + Line
     * - %class% class
     * - %type% type (::, ->)
     * - %function% function
     * - %arguments% Arguments
     */
    public function getTraceEntryTemplate(): string;

    public function getHr(): string;
}
