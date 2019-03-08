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

use Exception;
use Throwable;

/**
 * This class offers a wrapper for catching Exceptions.
 *
 * @patam mixed $var Message string or Exception.
 */
class CoreException extends Exception
{
    public function __construct($var, int $code = 0, Throwable $previous = null)
    {
        if ($var instanceof Exception) {
            $message = $var->getMessage();
            $code = $var->getCode();
            $previous = $var->getPrevious();
        } else {
            $message = (string) $var;
        }
        parent::__construct($message, $code, $previous);
    }
}