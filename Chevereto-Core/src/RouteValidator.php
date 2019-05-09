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

namespace Chevereto\Core;

use Throwable;

abstract class RouteValidator
{
    protected function processKeyValidation(string $key): void
    {
        try {
            Validation::grouped('$key', $key)
                ->append(
                    'value',
                    function (string $string): bool {
                        return
                            $string == '/' ?: (
                                strlen($string) > 0
                                && Utils\Str::startsWith('/', $string)
                                && !Utils\Str::endsWith('/', $string)
                                && !Utils\Str::contains('//', $string)
                                && !Utils\Str::contains(' ', $string)
                                && !Utils\Str::contains('\\', $string)
                            );
                    },
                    "String %i must start with a forward slash, it shouldn't contain neither whitespace, backslashes or extra forward slashes and it should be specified without a trailing slash."
                )
                ->append(
                    'wildcards',
                    function (string $string): bool {
                        return !$this->handlebars ?: preg_match_all('/{([0-9]+)}/', $string) === 0;
                    },
                    (string) (new Message('Wildcards in the form of %s are reserved.'))
                        ->code('%s', '/{n}')
                )
                ->validate();
        } catch (Throwable $e) {
            throw new RouteValidatorException($e);
        }
    }

    protected function processWildcardValidation(string $wildcardName, string $regex): void
    {
        $wildcard = $this->getHandlebarsWrap($wildcardName);
        try {
            Validation::grouped('$wildcardName', $wildcardName)
                ->append(
                    'value',
                    function (string $string): bool {
                        return
                            !Utils\Str::startsWithNumeric($string)
                            && preg_match('/^[a-z0-9_]+$/i', $string);
                    },
                    "String %s must contain only alphanumeric and underscore characters and it shouldn't start with a numeric value."
                )
                ->append(
                    'match',
                    function (string $string) use ($wildcard): bool {
                        return
                            Utils\Str::contains($wildcard, $this->getKey())
                            || Utils\Str::contains('{'."$string?".'}', $this->getKey());
                    },
                    (string) (new Message("Wildcard %s doesn't exists in %r."))
                        ->code('%s', $wildcard)
                        ->code('%r', $this->getKey())
                )
                ->append(
                    'unique',
                    function (string $string): bool {
                        return !isset($this->wheres[$string]);
                    },
                    (string) (new Message('Where clause for %s wildcard has been already declared.'))
                        ->code('%s', $wildcard)
                )
                ->validate();
            Validation::single(
                '$regex',
                $regex,
                function (string $string): bool {
                    return Validate::regex('/'.$string.'/');
                },
                'Invalid regex pattern %s.'
            );
        } catch (Exception $e) {
            throw new RouteException($e->getMessage());
        }
    }
}
class RouteValidatorException extends CoreException
{
}
