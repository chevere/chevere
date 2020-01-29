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

namespace Chevere\Components\Console;

use LogicException;
use Chevere\Components\Message\Message;
use Chevere\Components\App\Interfaces\BuilderInterface;
use Chevere\Components\Console\Interfaces\CommandInterface;
use Chevere\Components\Console\Interfaces\ConsoleInterface;
use Chevere\Components\Console\Interfaces\SymfonyCommandInterface;

/**
 * This is the base command of all Chevere commands.
 */
abstract class Command implements CommandInterface
{
    protected Console $console;

    protected SymfonyCommandInterface $symfony;

    final public function __construct(ConsoleInterface $console)
    {
        $this->console = $console;
        $this->setSymfony();
    }

    final public function console(): ConsoleInterface
    {
        return $this->console;
    }

    final public function symfony(): SymfonyCommandInterface
    {
        return $this->symfony;
    }

    final public function getArgumentString(string $argument): string
    {
        $string = $this->console->input()->getArgument($argument);
        $this->assertStringType($argument, $string);

        return $string;
    }

    final public function getArgumentArray(string $argument): array
    {
        $array = $this->console->input()->getArgument($argument);
        $this->assertArrayType($argument, $array);

        return $array;
    }

    final public function getOptionString(string $option): string
    {
        $string = $this->console->input()->getOption($option);
        $this->assertStringType($option, $string);

        return $string;
    }

    final public function getOptionArray(string $option): array
    {
        $array = $this->console->input()->getOption($option);
        $this->assertArrayType($option, $array);

        return $array;
    }

    abstract public function callback(BuilderInterface $builder): int;

    final private function setSymfony(): void
    {
        $this->symfony = (new SymfonyCommand(null))
            ->setName(static::NAME)
            ->setDescription(static::DESCRIPTION)
            ->setHelp(static::HELP);
        foreach (static::ARGUMENTS as $arguments) {
            $this->symfony->addArgument(...$arguments);
        }
        foreach (static::OPTIONS as $options) {
            $this->symfony->addOption(...$options);
        }
        $this->console = $this->console
            ->withCommand($this);
    }

    final private function assertStringType(string $for, $var): void
    {
        if (!is_string($var)) {
            throw new LogicException(
                $this->getWrongTypeMessage('string', gettype($var), $for)
            );
        }
    }

    final private function assertArrayType(string $for, $var): void
    {
        if (!is_array($var)) {
            throw new LogicException(
                $this->getWrongTypeMessage('array', gettype($var), $for)
            );
        }
    }

    final private function getWrongTypeMessage(string $expectedType, string $getType, string $for): string
    {
        $message = new Message('Expecting %expectedType% type, %getType% returned for %for%');

        return $message
            ->code('%expectedType%', $expectedType)
            ->code('%getType%', $getType)
            ->code('%for%', $for)
            ->toString();
    }
}
