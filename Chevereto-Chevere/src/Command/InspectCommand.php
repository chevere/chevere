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

namespace Chevereto\Chevere\Command;

use const Chevereto\Chevere\App\PATH;
use Chevereto\Chevere\App;
use Chevereto\Chevere\VarDumper\VarDumper;
use Chevereto\Chevere\Load;
use Chevereto\Chevere\Message;
use Chevereto\Chevere\Path;
use Chevereto\Chevere\Command;
use Chevereto\Chevere\File;
use Chevereto\Chevere\Utils\Str;
use Reflector;
use ReflectionMethod;
use ReflectionFunction;

/**
 * The InspectCommand allows to get callable information using CLI.
 *
 * Usage:
 * php app/console inspect "callable"
 */
class InspectCommand extends Command
{
    protected static $defaultName = 'inspect';

    /** @var array */
    protected $arguments = [];

    /** @var Reflector */
    protected $reflector;

    /** @var string */
    protected $callable;

    /** @var string */
    protected $method;

    /** @var string */
    protected $callableInput;

    /** @var string */
    protected $callableFilepath;

    protected function configure()
    {
        $this
            ->setDescription('Inspect any callable')
            ->setHelp('This command allows you to inspect any callable')
            ->addArgument('callable', Command::ARGUMENT_REQUIRED, 'The callable handle (name, fileHandle)');
    }

    public function callback(App $app): int
    {
        $cli = $this->getCli();
        $this->callableInput = (string) $cli->getInput()->getArgument('callable');
        $isCallable = is_callable($this->callableInput);
        if ($isCallable) {
            $this->callable = $this->callableInput;
            $callableSome = $this->callableInput;
        } else {
            $this->callableFilepath = Path::fromHandle($this->callableInput);
            if (!File::exists($this->callableFilepath)) {
                $cli->getIo()->error(sprintf('Unable to locate callable %s', $this->callableInput));

                return 0;
            }
            $callableSome = $this->callableFilepath;
            $this->callable = Load::php($this->callableFilepath);
            if (!is_callable($this->callable)) {
                $cli->getIo()->error(
                    (new Message('Expecting %t return type, %s provided in %f'))
                        ->code('%t', 'callable')
                        ->code('%s', gettype($this->callable))
                        ->code('%f', $this->callableInput)
                );

                return 0;
            }
        }

        $this->handleSetMethod();
        $this->handleSetReflector();
        $cli->getIo()->block($callableSome, 'INSPECTED', 'fg=black;bg=green', ' ', true);
        $this->processParametersArguments();
        $this->handleProcessArguments();

        return 1;
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
            //     $aux .= ' '.VarDumper::wrap(VarDumper::_OPERATOR, '--description '.$res['description'].' --regex '.$res['regex']);
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
        $this->getCli()->getIo()->text(['<fg=yellow>Arguments:</>']);
        $this->getCli()->getIo()->listing($this->arguments);
    }

    protected function processNoArguments(): void
    {
        $this->getCli()->getIo()->text(['<fg=yellow>No arguments</>', null]);
    }
}
