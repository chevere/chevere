<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\Path;

use InvalidArgumentException;
use LogicException;

use Chevere\Components\File\File;
use Chevere\Components\Message\Message;

use function ChevereFn\stringEndsWith;
use function ChevereFn\stringRightTail;

use const Chevere\APP_PATH;

final class PathHandle
{
    /** @var string */
    private $identifier;

    /** @var string Root context (absolute) */
    private $context;

    /** @var Path */
    private $path;

    /** @var File */
    private $file;

    /** @var string */
    private $filename;

    /** @var array */
    private $explode;

    /**
     * Path identifier refers to the standarized way in which files and paths
     * are handled by internal APIs like Hookable or Router.
     *
     * A path identifier looks like this:
     * dirname:file
     *
     * - The dirname is relative to APP_PATH
     * - dirname allows absolute paths
     *
     * @param string $identifier path identifier relative to app (<dirname>:<file>)
     */
    public function __construct(string $identifier)
    {
        $this->context = APP_PATH;
        $this->identifier = $identifier;
        $this->validateStringIdentifier();
        $this->validateCharIdentifier();
        $this->validateContext();
        $this->process();
    }

    public function identifier(): string
    {
        return $this->identifier;
    }

    public function path(): Path
    {
        return $this->path;
    }

    public function file(): File
    {
        return $this->file;
    }

    private function filenameFromIdentifier(): string
    {
        $this->explode = explode(':', $this->identifier);
        $end = end($this->explode);
        if (!$end) {
            throw new LogicException(
                (new Message('The identifier doesn\'t contain a file'))
                    ->toString()
            );
        }
        return $end;
    }

    private function validateStringIdentifier()
    {
        if (!($this->identifier != '' && !ctype_space($this->identifier))) {
            throw new InvalidArgumentException(
                (new Message('String %a needed, %v provided'))
                    ->code('%a', '$identifier')
                    ->code('%v', 'empty or null string')
                    ->toString()
            );
        }
    }

    private function validateCharIdentifier()
    {
        if (false !== strpos($this->identifier, ':')) {
            if (stringEndsWith(':', $this->identifier)) {
                throw new InvalidArgumentException(
                    (new Message('Wrong string %a format, %v provided (trailing colon)'))
                        ->code('%a', '$identifier')
                        ->code('%v', $this->identifier)
                        ->toString()
                );
            }
            $this->filename = $this->filenameFromIdentifier();
            if (false !== strpos($this->filename, '/')) {
                throw new InvalidArgumentException(
                    (new Message('Wrong string %a format, %v provided (path separators in filename)'))
                        ->code('%a', '$identifier')
                        ->code('%v', $this->identifier)
                        ->toString()
                );
            }
        }
    }

    private function validateContext()
    {
        $path = new Path($this->context);
        if (!$path->isDir()) {
            throw new InvalidArgumentException(
                (new Message('String %a must be an absolute path, %v provided'))
                    ->code('%a', '$context')
                    ->code('%v', $this->context)
                    ->toString()
            );
        }
    }

    private function process(): void
    {
        //var:build
        // $isPHP = stringEndsWith('.php', $this->identifier);
        $path = $this->identifier;
        $path = false !== strpos($path, ':')
            ? $this->processIdentifier()
            : $this->getPath();
        // if ($isPHP && (new File($this->identifier))->exists()) {
        //     $this->path = $path
        //         ->absolute($this->identifier);
        //     return;
        // }
        $this->path = (new Path($path))
            ->withContext($this->context);
        $this->file = new File($this->path);
    }

    private function processIdentifier(): string
    {
        if (pathinfo($this->filename, PATHINFO_EXTENSION) == null) {
            $this->filename .= '.php';
        }
        array_pop($this->explode);
        $path = join(':', $this->explode);
        if (strlen($path) > 0) {
            $path = stringRightTail($path, '/');
        }
        $path .= $this->filename;

        return $path;
    }

    private function getPath(): string
    {
        $extension = pathinfo($this->identifier, PATHINFO_EXTENSION);
        // No extension => add trailing slash to path
        if ($extension == false) {
            return stringRightTail($this->identifier, '/');
        }

        return $this->identifier;
    }
}
