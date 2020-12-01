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

/**
 * Describes the component in charge of orchestrating the var dump output processing.
 */
interface VarDumpOutputterInterface
{
    /**
     * This method is executed before `prepare()`.
     */
    public function setUp(WriterInterface $writer, array $backtrace);

    /**
     * Provides access to the instance backtrace.
     */
    public function backtrace(): array;

    /**
     * Provides access to the instance caller.
     */
    public function caller(): string;

    /**
     * This method is executed before `tearDown()`.
     */
    public function prepare(): void;

    /**
     * Ends the outputter.
     */
    public function tearDown(): void;
}
