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

abstract class RouteProcessor extends RouteValidator
{
    protected function processMaker(): void
    {
        $maker = debug_backtrace(0, 1)[0];
        $maker['file'] = Path::relative($maker['file']);
        $this->maker = $maker;
    }

    protected function processWildcards(): void
    {
        if ($this->handlebars && preg_match_all(static::REGEX_WILDCARD_SEARCH, $this->key, $matches)) {
            // $matches[0] => [{wildcard}, {wildcard?},...]
            // $matches[1] => [wildcard, wildcard?,...]
            // Build the route handle, needed for regex replacements
            $this->set = $this->key;
            // Build the optionals array, needed for creating route power set if needed
            $this->optionals = [];
            $this->optionalsIndex = [];
            $this->processWildcardMatches($matches);
            $this->processOptionals();
        }
    }

    protected function processOptionals(): void
    {
        // Determine if route contains optional wildcards
        if (!empty($this->optionals)) {
            $mandatoryDiff = array_diff($this->wildcards ?? [], $this->optionalsIndex);
            $this->mandatoryIndex = [];
            foreach ($mandatoryDiff as $k => $v) {
                $this->mandatoryIndex[$k] = null;
            }
            // Generate the optionals power set, keeping its index keys in case of duplicated optionals
            $powerSet = Utils\Arr::powerSet($this->optionals, true);
            // Build the route set, it will contain all the possible route combinations
            $this->processPowerSet($powerSet);
        }
    }

    protected function processPowerSet(array $powerSet): void
    {
        $routeSet = [];
        foreach ($powerSet as $set) {
            $auxSet = $this->set;
            // auxWildcards keys represent the wildcards being used. Iterate it with foreach.
            $auxWildcards = $this->mandatoryIndex;
            foreach ($set as $replaceKey => $replaceValue) {
                $replace = $this->optionals[$replaceKey];
                if ($replaceValue !== null) {
                    $replaceValue = "{{$replaceValue}}";
                    $auxWildcards[$replace] = null;
                }
                $auxSet = str_replace("{{$replace}}", $replaceValue ?? '', $auxSet);
                $auxSet = Path::normalize($auxSet);
            }
            ksort($auxWildcards);
            /*
             * Maps expected regex indexed matches [0,1,2,] to registered wildcard index [index=>n].
             * For example, a set /test-{0}--{2} will capture 0->0 and 1->2. Storing the expected index allows\
             * to easily map matches => wildcards => values.
             */
            $routeSet[$auxSet] = array_keys($auxWildcards);
        }
        $this->powerSet = $routeSet;
    }

    protected function processWildcardMatches(array $matches): void
    {
        foreach ($matches[0] as $k => $v) {
            // Change {wildcard} to {n} (n is the wildcard index)
            if (isset($this->set)) {
                $this->set = Utils\Str::replaceFirst($v, "{{$k}}", $this->set);
            }
            $wildcard = $matches[1][$k];
            if (Utils\Str::endsWith('?', $wildcard)) {
                $wildcardTrim = Utils\Str::replaceLast('?', null, $wildcard);
                $this->optionals[] = $k;
                $this->optionalsIndex[$k] = $wildcardTrim;
            } else {
                $wildcardTrim = $wildcard;
            }
            if (in_array($wildcardTrim, $this->wildcards ?? [])) {
                throw new RouteException(
                    (new Message('Must declare one unique wildcard per capturing group, duplicated %s detected in route %r.'))
                        ->code('%s', $matches[0][$k])
                        ->code('%r', $this->key)
                );
            }
            $this->wildcards[] = $wildcardTrim;
        }
    }
}
