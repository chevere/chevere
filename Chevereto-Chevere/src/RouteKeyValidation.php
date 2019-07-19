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

class RouteKeyValidation
{
    /** @var string */
    public $key;

    /** @var bool */
    public $hasHandlebars;

    public function __construct(string $key)
    {
        $this->key = $key;
        $this->hasHandlebars = $this->hasHandlebars($this->key);
        $this->handleValidateFormat();
        $this->handleWildcards();
    }

    protected function handleValidateFormat()
    {
        if (!$this->validateFormat($this->key)) {
            throw new CoreException(
                (new Message("String %s must start with a forward slash, it shouldn't contain neither whitespace, backslashes or extra forward slashes and it should be specified without a trailing slash."))
                    ->code('%s', $this->key)
            );
        }
    }

    protected function handleWildcards()
    {
        if ($this->hasHandlebars && !$this->validateWildcard($this->key)) {
            throw new CoreException(
                (new Message('Wildcards in the form of %s are reserved.'))
                    ->code('%s', '/{n}')
            );
        }
    }

    protected function validateFormat(string $key): bool
    {
        if ('/' == $key) {
            return true;
        }

        return strlen($key) > 0 && Utility\Str::startsWith('/', $key)
            && $this->validateFormatSlashes($key);
    }

    protected function validateFormatSlashes(string $key): bool
    {
        return !Utility\Str::endsWith('/', $key)
            && !Utility\Str::contains('//', $key)
            && !Utility\Str::contains(' ', $key)
            && !Utility\Str::contains('\\', $key);
    }

    protected function validateWildcard(string $key): bool
    {
        return preg_match_all('/{([0-9]+)}/', $key) === 0;
    }

    protected function hasHandlebars(string $key): bool
    {
        return Utility\Str::contains('{', $key) || Utility\Str::contains('}', $key);
    }
}
