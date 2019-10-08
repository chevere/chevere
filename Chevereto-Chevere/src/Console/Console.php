<?php

declare(strict_types=1);

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevere\Console;

use RuntimeException;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Chevere\Message\Message;
use Symfony\Component\Console\Application as Symfony;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\StyleInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Chevere\Console\Commands\BuildCommand;
use Chevere\Console\Commands\ClearLogsCommand;
use Chevere\Console\Commands\DestroyCommand;
use Chevere\Console\Commands\RequestCommand;
use Chevere\Console\Commands\RunCommand;
use Chevere\Console\Commands\InspectCommand;
use Chevere\Contracts\App\BuilderContract;
use Chevere\Contracts\Console\CommandContract;
use Chevere\Contracts\Console\ConsoleContract;

/**
 * Provides static access to the Chevere application console.
 */
final class Console implements ConsoleContract
{
    const NAME = __NAMESPACE__;
    const VERSION = '1.0';

    const VERBOSITY_QUIET = ConsoleOutput::VERBOSITY_QUIET;
    const VERBOSITY_NORMAL = ConsoleOutput::VERBOSITY_NORMAL;
    const VERBOSITY_VERBOSE = ConsoleOutput::VERBOSITY_VERBOSE;
    const VERBOSITY_VERY_VERBOSE = ConsoleOutput::VERBOSITY_VERY_VERBOSE;
    const VERBOSITY_DEBUG = ConsoleOutput::VERBOSITY_DEBUG;

    const OUTPUT_NORMAL = ConsoleOutput::OUTPUT_NORMAL;
    const OUTPUT_RAW = ConsoleOutput::OUTPUT_RAW;
    const OUTPUT_PLAIN = ConsoleOutput::OUTPUT_PLAIN;

    /** @var ArgvInput */
    private static $input;

    /** @var ConsoleOutput */
    private static $output;

    /** @var CommandContract */
    private static $command;

    /** @var string The first argument (command) passed */
    private static $commandString;

    /** @var Symfony */
    private static $symfony;

    /** @var SymfonyStyle */
    private static $style;

    /** @var bool */
    private static $isAvailable;

    /** @var BuilderContract */
    private static $builder;

    public function __construct()
    {
        self::$input = new ArgvInput();
        self::$output = new ConsoleOutput();
        self::$commandString = self::$input->getFirstArgument();
        self::$symfony = new Symfony(static::NAME, static::VERSION);
        self::$symfony->setAutoExit(false);
        self::$style = new SymfonyStyle(self::$input, self::$output);
        self::$isAvailable = true;
        $this->addCommands();
    }

    public static function input(): InputInterface
    {
        return self::$input;
    }

    public static function output(): OutputInterface
    {
        return self::$output;
    }

    public function withCommand(CommandContract $command): ConsoleContract
    {
        $new = clone $this;
        $new::$command = $command;

        return $new;
    }

    public static function command(): CommandContract
    {
        return self::$command;
    }

    public static function commandString(): string
    {
        return self::$commandString;
    }

    public static function symfony(): Symfony
    {
        return self::$symfony;
    }

    public static function style(): StyleInterface
    {
        return self::$style;
    }

    public static function inputString(): string
    {
        return self::$input->__toString();
    }

    public static function isBuilding(): bool
    {
        return self::$isAvailable && 'build' == self::$commandString;
    }

    public static function bind(BuilderContract $builder): bool
    {
        if (php_sapi_name() == 'cli') {
            self::$builder = $builder;

            return true;
        }

        return false;
    }

    public static function run()
    {
        if (!self::$isAvailable) {
            throw new RuntimeException(
                (new Message('Unable to call %method% when %class% is not available.'))
                    ->code('%method%', __METHOD__)
                    ->code('%class%', __CLASS__)
                    ->toString()
            );
        }
        $exitCode = self::runner();
        if (0 !== $exitCode) {
            exit($exitCode);
        }
        if (is_null(self::$command)) {
            exit($exitCode);
        }
        if (self::$builder == null) {
            throw new RuntimeException(
                (new Message('No Chevere %className% instance is defined'))
                    ->code('%className%', BuilderContract::class)
                    ->toString()
            );
        }
        exit(self::$command->callback(self::$builder));
    }

    private function addCommands(): void
    {
        self::$symfony->addCommands([
            (new BuildCommand($this))->symfony(),
            (new ClearLogsCommand($this))->symfony(),
            (new RequestCommand($this))->symfony(),
            (new RunCommand($this))->symfony(),
            (new InspectCommand($this))->symfony(),
            (new DestroyCommand($this))->symfony(),
        ]);
        if (!self::$symfony->has(self::$commandString)) {
            self::$style->writeln(sprintf('Command "%s" is not defined', self::$commandString));
            die(127);
        }
    }

    /**
     * Runs the current command.
     *
     * @return int 0 if everything went fine, or an error code
     *
     * @throws Exception When running fails. Bypass this when {@link setCatchExceptions()}.
     */
    private static function runner(): int
    {
        return self::$symfony->run(
            self::$input,
            self::$output
        );
    }
}
