<?php declare(strict_types=1);
/*
 * This file is part of Chevereto\Core.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Chevereto\Core;

use Symfony\Component\Console\Output\ConsoleOutput as BaseOutput;

/**
 * ConsoleOutput with extended support for storing and retrieving values in command context.
 * Needed to provide a method in which the Core::App can access to console commands.
 */
class ConsoleOutput extends BaseOutput
{
    protected $chvBag = [];
    /**
     * Stores a variable in the BufferedOutput.
     *
     * @param string $key Variable key.
     * @param string $var A variable.
     */
    public function storeVar(string $key, $var) : void
    {
        $this->chvBag[$key] = $var;
    }
    /**
     * Retrieves a variable value.
     *
     * @param string $key Variable key.
     */
    public function getVar(string $key)
    {
        return $this->chvBag[$key] ?? null;
    }
}
