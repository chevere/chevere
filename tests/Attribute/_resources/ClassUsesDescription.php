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

namespace Chevere\Tests\Attribute\_resources;

use Chevere\Attributes\Description;

#[Description('Class')]
final class ClassUsesDescription
{
    #[Description('Constant')]
    public const CONSTANT = 'constant';

    #[Description('Property')]
    private string $property;

    #[Description('Method')]
    public function run(
        #[Description('Parameter')]
        string $parameter
    ): void {
    }
}

#[Description('Function')]
function functionUsesDescription(
    #[Description('Parameter')]
    string $parameter
): string {
    return $parameter;
}
