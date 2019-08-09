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

namespace Chevere;

use InvalidArgumentException;

final class PathHandle
{
    /** @var string */
    private $identifier;

    /** @var string|null */
    private $context;

    /** @var string */
    private $path;

    /** @var string|null */
    private $filename;

    /** @var array */
    private $explode;

    /**
     * @param string $identifier Path identifier (<dirname>:<file>)
     * @param string $context    Root context for $identifier. Must be absolute path.
     */
    public function __construct(string $identifier, string $context)
    {
        $this->identifier = $identifier;
        $this->context = $context;
        $this->validateStringIdentifier();
        $this->validateCharIdentifier();
        $this->validateContext();
        $this->process();
    }

    public function identifier(): string
    {
        return $this->identifier;
    }

    public function path(): string
    {
        return $this->path;
    }

    private function filenameFromIdentifier(): string
    {
        $this->explode = explode(':', $this->identifier);

        return end($this->explode);
    }

    private function validateStringIdentifier()
    {
        if (!($this->identifier != '' && !ctype_space($this->identifier))) {
            throw new InvalidArgumentException(
                (new Message('String %a needed, %v provided.'))
                    ->code('%a', '$identifier')
                    ->code('%v', 'empty or null string')
                    ->toString()
            );
        }
    }

    private function validateCharIdentifier()
    {
        if (Utility\Str::contains(':', $this->identifier)) {
            if (Utility\Str::endsWith(':', $this->identifier)) {
                throw new InvalidArgumentException(
                    (new Message('Wrong string %a format, %v provided (trailing colon).'))
                        ->code('%a', '$identifier')
                        ->code('%v', $this->identifier)
                        ->toString()
                );
            }
            $this->filename = $this->filenameFromIdentifier();
            if (Utility\Str::contains('/', $this->filename)) {
                throw new InvalidArgumentException(
                    (new Message('Wrong string %a format, %v provided (path separators in filename).'))
                        ->code('%a', '$identifier')
                        ->code('%v', $this->identifier)
                        ->toString()
                );
            }
        }
    }

    private function validateContext()
    {
        if (!Path::isAbsolute($this->context)) {
            throw new InvalidArgumentException(
                (new Message('String %a must be an absolute path, %v provided.'))
                    ->code('%a', '$context')
                    ->code('%v', $this->context)
                    ->toString()
            );
        }
    }

    private function process()
    {
        if (Utility\Str::endsWith('.php', $this->identifier) && File::exists($this->identifier)) {
            return Path::isAbsolute($this->identifier) ? $this->identifier : Path::absolute($this->identifier);
        }
        $this->path = Path::normalize($this->identifier);
        if (Utility\Str::contains(':', $this->path)) {
            $this->path = $this->processIdentifier();
        } else {
            $this->path = $this->processPath();
        }
        // $this->path is not an absolute path neither a wrapper or anything like that
        if (!Path::isAbsolute($this->path)) {
            $this->path = $this->context.$this->path;
        }
        // Resolve . and ..
        $this->path = Path::resolve($this->path);
    }

    private function processIdentifier(): string
    {
        if (pathinfo($this->filename, PATHINFO_EXTENSION) == null) {
            $this->filename .= '.php';
        }
        array_pop($this->explode);
        $path = join(':', $this->explode);
        if (strlen($path) > 0) {
            $path = Path::tailDir($path);
        }
        $path .= $this->filename;

        return $path;
    }

    private function processPath(): string
    {
        // If $this->path does't contains ":", we assume that it is a directory or a explicit filepath
        $extension = pathinfo($this->path, PATHINFO_EXTENSION);
        // No extension => add trailing slash to path
        if ($extension == false) {
            return Path::tailDir($this->path);
        }

        return $this->path;
    }
}
