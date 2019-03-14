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

use Chevereto\Core\App;

interface DataInterface
{
    public function addDataKey(string $key, $var);
    public function setDataKey(string $key, $var);
    public function removeDataKey(string $key);
}