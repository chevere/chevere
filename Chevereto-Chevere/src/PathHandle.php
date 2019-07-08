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
        $this->path = Path::normalize($this->identifier);
        if (Utils\Str::contains(':', $this->path)) {
            $this->processIdentifier();
        } else {
            // If $this->path does't contains ":", we assume that it is a directory or a explicit filepath
            $extension = pathinfo($this->path, PATHINFO_EXTENSION);
            // No extension => add trailing slash to path
            if ($extension == false) {
                $this->path = Path::tailDir($this->path);
            }
        }
        // $this->path is not an absolute path neither a wrapper or anything like that
        if (!Path::isAbsolute($this->path)) {
            $this->path = $this->context.$this->path;
        }
        // Resolve . and ..
        $this->path = Path::resolve($this->path);
    }

    protected function processIdentifier()
    {
        $explode = explode(':', $this->path);
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
                $this->path = join(':', $explode);
                if (strlen($this->path) > 0) {
                    $this->path = Path::tailDir($this->path);
                }
                $this->path .= $filename;
            }
        }
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
