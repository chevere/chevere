<?php declare(strict_types=1);
/*
 * This file is part of Chevereto\Core.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Chevereto\Core\Interfaces;

// docs

interface ControllerInterface
{
    /**
     * All controllers must include the magic __invoke().
     *
     * @param mixed $parameters Controller input parameters.
     */
    // todo mejor validacion del invoke
    // public function __invoke($parameters = null, $filter = null);
}
