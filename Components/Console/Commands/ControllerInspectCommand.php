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

namespace Chevere\Components\Console\Commands;

use Ahc\Cli\Exception\RuntimeException;
use Ahc\Cli\Input\Command;
use Chevere\Components\Controller\ControllerName;
use Chevere\Components\Controller\ControllerParameter;
use Chevere\Exceptions\Controller\ControllerNotExistsException;

/**
 * @codeCoverageIgnore
 *
 * @property string $fqn
 */
final class ControllerInspectCommand extends Command
{
    public function __construct()
    {
        parent::__construct('coninspect', 'Inspect a controller');
        $this
            ->argument('<fqn>', 'Controller full-qualified name')
            ->usage(
                '<bold>  coninspect</end> <comment>"App\Controllers\TheController"</end><eol/>'
            );
    }

    public function execute(): int
    {
        try {
            $controllerName = (new ControllerName($this->fqn))->toString();
        } catch (ControllerNotExistsException $e) {
            throw new RuntimeException("Controller doesn't exists");
        }
        /**
         * @var ControllerInterface $controller
         */
        $controller = new $controllerName;
        $description = $controller->description();
        $this->writer()
            ->okBold('Inspect ' . $controllerName)
            ->eol(2)
            ->infoBold('Description', true)
            ->comment($description !== '' ? $description : 'no description')
            ->eol(2);
        if (count($controller->parameters()->map()) > 0) {
            $this->writer()->infoBold('Parameters', true);
            /**
             * @var ControllerParameter $parameter
             */
            $table = [];
            foreach ($controller->parameters()->map() as $parameter) {
                $required = ($parameter->isRequired() ? 'true' : 'false');
                $table[] = [
                    'Name' => $parameter->name(),
                    'Regex' => $parameter->regex()->toString(),
                    'Required' => $required
                ];
            }
            $this->writer()->table($table, [
                'head' => 'yellow',
                'even' => 'comment',
            ]);
        }

        return 0;
    }
}
