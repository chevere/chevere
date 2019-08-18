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

namespace Chevere\Cache;

use LogicException;
use Chevere\Message;
use Chevere\Route\Route;
use Chevere\Contracts\Route\RouteContract;
use Chevere\File;
use Chevere\FileReturn\Maker as FileReturnMaker;
use Chevere\Path\PathHandle;
use InvalidArgumentException;

/**
 * Makes a simple PHP file return
 */
final class Maker
{

    /** @var Key */
    private $key;

    /** @var string Chache name (user input) */
    private $name;

    public function __construct(string $name)
    {
        $this->key = new Key($name);
        $this->name = $name;
    }

    /**
     * Put cache, return the file signature
     */
    public function put(string $key, $var): string
    {
        $fileIdentifier = $this->key->genFileIdentifier($key);
        $pathHandle = new PathHandle($fileIdentifier);
        $fileReturnMaker = new FileReturnMaker($pathHandle, $var);
        
        return $fileReturnMaker->checksum();
    }
}
