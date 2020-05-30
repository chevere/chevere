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

use Ahc\Cli\Input\Command;
use Chevere\Components\Controller\ControllerName;
use Chevere\Components\Controller\ControllerParameter;
use Chevere\Components\Message\Message;
use Chevere\Exceptions\Controller\ControllerNotExistsException;

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
            $this->writer()->error(
                (new Message("Controller %controllerName% doesn't exists"))
                    ->code('%controllerName%', $this->fqn)
                    ->toString(),
                true
            );

            return 127;
        }
        /**
         * @var ControllerInterface $controller
         */
        $controller = new $controllerName;
        $description = $controller->description();
        $this->writer()
            ->ok('Inspect ' . $controllerName, true)
            ->eol()
            ->bold('Description', true)
            ->comment($description !== '' ? $description : 'no description', true)
            ->eol();
        if (count($controller->parameters()->map()) > 0) {
            $this->writer()->bold('Parameters', true);
            /**
             * @var ControllerParameter $parameter
             */
            $table = [];
            foreach ($controller->parameters()->map() as $parameter) {
                $required = ($parameter->isRequired() ? 'true' : 'false');
                $table[] = ['Name' => $parameter->name(), 'Regex' => $parameter->regex()->toString(), 'Required' => $required];
            }
            $this->writer()->table($table);
        }

        return 0;
    }
}
