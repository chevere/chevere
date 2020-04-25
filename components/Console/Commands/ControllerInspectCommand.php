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
use Ahc\Cli\Output\Writer;
use Chevere\Components\Controller\ControllerName;
use Chevere\Components\Controller\ControllerParameter;
use Chevere\Components\Controller\Interfaces\ControllerInterface;
use Throwable;

final class ControllerInspectCommand extends Command
{
    public function __construct()
    {
        parent::__construct('coninspect', 'Inspect a controller');

        $this
            ->argument('<fqn>', 'Controller full-qualified name')
            ->option('-n --no-hooks', 'No hooks', 'boolval', false)
            ->usage(
                '<bold>  $0 coninspect</end> <comment>"App\Controllers\TheController"</end><eol/>'
            );
    }

    public function execute()
    {
        try {
            $controllerName = (new ControllerName($this->fqn))->toString();
        } catch (Throwable $e) {
            return 1;
        }
        /**
         * @var ControllerInterface $controller
         */
        $controller = new $controllerName;
        $parametersMap = $controller->parameters()->map();
        if ($parametersMap->count() == 0) {
        } else {
        }

        $this->writer()->colors('<bold>Controller arguments</end>', true);
        $this->writer()->colors('<blue>+------------------+</end>', true);
        /**
         * @var ControllerParameter $parameter
         */
        foreach ($parametersMap as $parameter) {
            $this->writer()->colors('Name ' . $parameter->name() . '<eol>');
            $this->writer()->colors('Regex ' . $parameter->regex()->toString() . '<eol>');
            $required = ($parameter->isRequired() ? '<purple>true' : '<comment>false') . '</end>';
            $this->writer()->colors('Required ' . $required . '<eol>');
            $this->writer()->colors('<blue>+------------------+</end>', true);
        }
    }
}
