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

namespace Chevere\Path;

use const Chevere\APP_PATH;

use InvalidArgumentException;
use Chevere\File;
use Chevere\Message;
use Chevere\Utility\Str;
use LogicException;

final class PathHandle
{
    /** @var string */
    private $identifier;

    /** @var string */
    private $context = APP_PATH;

    /** @var string absolute path like /home/user/app/ or /home/user/app/file.php */
    private $path;

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

    public function path(): string
    {
        return $this->path;
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
                (new Message('String %a needed, %v provided.'))
                    ->code('%a', '$identifier')
                    ->code('%v', 'empty or null string')
                    ->toString()
            );
        }
    }

    private function validateCharIdentifier()
    {
        if (Str::contains(':', $this->identifier)) {
            if (Str::endsWith(':', $this->identifier)) {
                throw new InvalidArgumentException(
                    (new Message('Wrong string %a format, %v provided (trailing colon).'))
                        ->code('%a', '$identifier')
                        ->code('%v', $this->identifier)
                        ->toString()
                );
            }
            $this->filename = $this->filenameFromIdentifier();
            if (Str::contains('/', $this->filename)) {
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
        if (Str::endsWith('.php', $this->identifier) && File::exists($this->identifier)) {
            return Path::isAbsolute($this->identifier) ? $this->identifier : Path::absolute($this->identifier);
        }
        $this->path = Path::normalize($this->identifier);
        if (Str::contains(':', $this->path)) {
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
