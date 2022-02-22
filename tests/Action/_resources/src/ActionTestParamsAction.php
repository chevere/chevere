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

namespace Chevere\Tests\Action\_resources\src;

use Chevere\Action\Action;
use Chevere\Filesystem\Interfaces\FileInterface;
use Chevere\Response\Interfaces\ResponseInterface;

final class ActionTestParamsAction extends Action
{
    public function run(
        int $int,
        string $string,
        bool $bool,
        float $float,
        array $array,
        object $object,
        FileInterface $file,
        int $intDefault = 1,
        string $stringDefault = 'default',
        bool $boolDefault = false,
        float $floatDefault = 0.0,
        array $arrayDefault = [],
        object $objectDefault = null,
    ): ResponseInterface {
        return $this->getResponse();
    }
}
