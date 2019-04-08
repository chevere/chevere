<?php

declare(strict_types=1);

/*
 * This file is part of Chevereto\Core.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevereto\Core;

use RecursiveIterator;
use RecursiveFilterIterator;

/**
 * Provides filtering for the Api register process (directory scan).
 */
class ApisFilterIterator extends RecursiveFilterIterator
{
    /** @var array HTTP methods accepted by this filter [HTTP_METHOD,] */
    const ACCEPT_METHODS = Route::HTTP_METHODS;

    /** @var array Special accepted files [filename.ext,] */
    const ACCEPT_FILES = ['resource.json'];

    /** @var array The usable accepted files array [HTTP_METHOD.php, filename.ext,] */
    protected static $acceptedFiles;

    public function __construct(RecursiveIterator $iterator)
    {
        parent::__construct($iterator);
        static::$acceptedFiles = array_merge(array_map(function ($val) {
            return $val.'.php';
        }, static::ACCEPT_METHODS), static::ACCEPT_FILES);
    }

    public function accept()
    {
        return $this->current()->isDir()
          ? true
          : in_array($this->current()->getFilename(), static::$acceptedFiles
        );
    }
}
