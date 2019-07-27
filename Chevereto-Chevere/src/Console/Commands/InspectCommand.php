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

// TODO: Static inspection!
// TODO: Deprecate callables by file
// TODO: Non-ambiguous types. Use $callableString $callableObject?

namespace Chevere\Console\Commands;

use Reflector;
use ReflectionMethod;
use ReflectionFunction;
use const Chevere\App\PATH;
use Chevere\App\App;
use Chevere\Console\Command;
use Chevere\Load;
use Chevere\Message;
use Chevere\Path;
use Chevere\File;
use Chevere\Utility\Str;

/**
 * The InspectCommand allows to get callable information using CLI.
 *
 * Usage:
 * php app/console inspect "callable"
 */
final class InspectCommand extends Command
{
    protected static $defaultName = 'inspect';

    /** @var array */
    protected $arguments = [];

    /** @var Reflector */
    protected $reflector;

    /** @var object|string */
    protected $callable;

    /** @var string */
    protected $method;

    /** @var string */
    protected $callableInput;

    /** @var string */
    protected $callableFilepath;

    public function callback(App $app): int
    {
        $this->callableInput = (string) $this->cli->input->getArgument('callable');
        $isCallable = is_callable($this->callableInput);
        if ($isCallable) {
            $this->callable = $this->callableInput;
            $callableSome = $this->callableInput;
        } else {
            $this->callableFilepath = Path::fromHandle($this->callableInput);
            if (!File::exists($this->callableFilepath)) {
                $this->cli->out->error(sprintf('Unable to locate callable %s', $this->callableInput));

                return 0;
            }
            $callableSome = $this->callableFilepath;
            $this->callable = Load::php($this->callableFilepath);
            if (!is_callable($this->callable)) {
                $this->cli->out->error(
                    (new Message('Expecting %t return type, %s provided in %f'))
                        ->code('%t', 'callable')
                        ->code('%s', gettype($this->callable))
                        ->code('%f', $this->callableInput)
                        ->toString()
                );

                return 0;
            }
        }

        $this->handleSetMethod();
        $this->handleSetReflector();
        $this->cli->out->block($callableSome, 'INSPECTED', 'fg=black;bg=green', ' ', true);
        $this->processParametersArguments();
        $this->handleProcessArguments();

        return 1;
    }

    protected function configure()
    {
        $this
            ->setDescription('Inspect any callable')
            ->setHelp('This command allows you to inspect any callable')
            ->addArgument('callable', Command::ARGUMENT_REQUIRED, 'The callable handle (name, fileHandle)');
    }

    protected function handleSetMethod(): void
    {
        if (is_object($this->callable)) {
            $this->method = '__invoke';
        } else {
            if (Str::contains('::', $this->callable)) {
                $callableExplode = explode('::', $this->callable);
                $this->callable = $callableExplode[0];
                $this->method = $callableExplode[1];
            }
        }
    }

    protected function handleSetReflector(): void
    {
        if (isset($this->method)) {
            $this->setReflectionMethod();
        } else {
            $this->setReflectionFunction();
        }
    }

    protected function setReflectionMethod()
    {
        $this->reflector = new ReflectionMethod($this->callable, $this->method);
    }

    protected function setReflectionFunction()
    {
        $this->reflector = new ReflectionFunction($this->callable);
    }

    protected function processParametersArguments(): void
    {
        $i = 0;
        foreach ($this->reflector->getParameters() as $parameter) {
            $aux = null;
            if ($parameter->getType()) {
                $aux .= $parameter->getType().' ';
            }
            $aux .= '$'.$parameter->getName();
            if ($parameter->isDefaultValueAvailable()) {
                $aux .= ' = '.($parameter->getDefaultValue() ?? 'null');
            }
            // $res = $resource[$parameter->getName()] ?? null;
            // if (isset($res)) {
            //     $aux .= ' '.VarDump::wrap(VarDump::_OPERATOR, '--description '.$res['description'].' --regex '.$res['regex']);
            // }
            $this->arguments[] = "#$i $aux";
            ++$i;
        }
    }

    protected function handleProcessArguments(): void
    {
        if (null != $this->arguments) {
            $this->processArguments();
        } else {
            $this->processNoArguments();
        }
    }

    protected function processArguments(): void
    {
        $this->cli->out->text(['<fg=yellow>Arguments:</>']);
        $this->cli->out->listing($this->arguments);
    }

    protected function processNoArguments(): void
    {
        $this->cli->out->text(['<fg=yellow>No arguments</>', null]);
    }
}
