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

namespace Chevere\Components\Console\Interfaces;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Chevere\Components\App\Interfaces\BuilderInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

interface ConsoleInterface
{
    const NAME = 'Chevere';
    const VERSION = '1.0';

    const VERBOSITY_QUIET = ConsoleOutput::VERBOSITY_QUIET;
    const VERBOSITY_NORMAL = ConsoleOutput::VERBOSITY_NORMAL;
    const VERBOSITY_VERBOSE = ConsoleOutput::VERBOSITY_VERBOSE;
    const VERBOSITY_VERY_VERBOSE = ConsoleOutput::VERBOSITY_VERY_VERBOSE;
    const VERBOSITY_DEBUG = ConsoleOutput::VERBOSITY_DEBUG;

    const OUTPUT_NORMAL = ConsoleOutput::OUTPUT_NORMAL;
    const OUTPUT_RAW = ConsoleOutput::OUTPUT_RAW;
    const OUTPUT_PLAIN = ConsoleOutput::OUTPUT_PLAIN;

    public function __construct();

    public function withCommand(CommandInterface $command): ConsoleInterface;

    public function hasCommand(): bool;

    public function input(): InputInterface;

    public function output(): OutputInterface;

    public function style(): SymfonyStyle;

    public function command(): CommandInterface;

    public function inputString(): string;

    public function isBuilding(): bool;

    public function bind(BuilderInterface $builder): bool;

    public function run();
}
