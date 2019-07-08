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

namespace Chevereto\Chevere;

class PathHandle
{
    /** @var string */
    public $identifier;

    /** @var string|null */
    public $context;

    /** @var string */
    protected $path;

    /**
     * @param string $identifier Path identifier (<dirname>:<file>)
     */
    public function __construct(string $identifier)
    {
        $this->validateIdentifier($identifier);
        $this->identifier = $identifier;
    }

    /**
     * @param string $context Root context for $identifier. Must be absolute path.
     */
    public function setContext(string $context): self
    {
        $this->context = $context;

        return $this;
    }

    public function validateIdentifier(string $identifier)
    {
        if (!($identifier != '' && !ctype_space($identifier))) {
            throw new CoreException(
                (new Message('String %a needed, %v provided.'))
                    ->code('%a', '$identifier')
                    ->code('%v', ' empty or null string')
            );
        }
    }

    public function validateContext(string $context)
    {
        if (!Path::isAbsolute($context)) {
            throw new CoreException(
                (new Message('String %a must be an absolute path, %v provided.'))
                    ->code('%a', '$context')
                    ->code('%v', $context)
            );
        }

        return $this;
    }

    public function process()
    {
        if (Utils\Str::endsWith('.php', $this->identifier) && File::exists($this->identifier)) {
            return Path::isAbsolute($this->identifier) ? $this->identifier : Path::absolute($this->identifier);
        }
        $path = Path::normalize($this->identifier);
        if (Utils\Str::contains(':', $path)) {
            $explode = explode(':', $path);
            $filename = end($explode);
            if (is_string($filename)) {
                // Last prop doesn't look like a filename
                if (Utils\Str::contains('/', $filename)) {
                    unset($filename);
                } else {
                    // Append .php by default
                    if (pathinfo($filename, PATHINFO_EXTENSION) == null) {
                        $filename .= '.php';
                    }
                    // Unset the last element (file) from $explode
                    array_pop($explode);
                    // Rebuild path
                    $path = join(':', $explode);
                    if (strlen($path) > 0) {
                        $path = Path::tailDir($path);
                    }
                    $path .= $filename;
                }
            }
        } else {
            // If $path does't contains ":", we assume that it is a directory or a explicit filepath
            $extension = pathinfo($path, PATHINFO_EXTENSION);
            // No extension => add trailing slash to path
            if ($extension == false) {
                $path = Path::tailDir($path);
            }
        }
        // $path is not an absolute path neither a wrapper or anything like that
        if (!Path::isAbsolute($path)) {
            $path = $this->context.$path;
        }
        // Resolve . and ..
        $this->path = Path::resolve($path);
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
