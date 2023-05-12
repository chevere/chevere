<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevere.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\VariableSupport\Traits;

use Chevere\Iterator\Interfaces\BreadcrumbInterface;
use Chevere\Throwable\Exceptions\OutOfBoundsException;
use Chevere\VariableSupport\Exceptions\ObjectNotClonableException;

trait BreadcrumbIterableTrait
{
    private BreadcrumbInterface $breadcrumb;

    abstract private function assert(mixed $variable): void;

    /**
     * @param iterable<mixed, mixed> $variable
     * @throws ObjectNotClonableException
     * @throws OutOfBoundsException
     */
    private function breadcrumbIterable(iterable $variable, string ...$callable): void
    {
        $this->breadcrumb = $this->breadcrumb->withAdded('(iterable)');
        $iterableKey = $this->breadcrumb->pos();
        foreach ($variable as $key => $value) {
            $key = $this->getKey($key);
            $this->breadcrumb = $this->breadcrumb
                ->withAdded('key: ' . $key);
            $memberKey = $this->breadcrumb->pos();
            $this->assert($value, ...$callable);
            $this->breadcrumb = $this->breadcrumb
                ->withRemoved($memberKey);
        }
        $this->breadcrumb = $this->breadcrumb
            ->withRemoved($iterableKey);
    }

    /**
     * @infection-ignore-all
     */
    private function getKey(mixed $key): string
    {
        return match (true) {
            is_scalar($key) => strval($key),
            default => '<none>',
        };
    }
}
