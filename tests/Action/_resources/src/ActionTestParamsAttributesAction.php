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

namespace Chevere\Tests\Action\_resources\src;

use Chevere\Action\Action;
use Chevere\Common\Attributes\DescriptionAttribute;
use Chevere\Regex\Attributes\RegexAttribute;

final class ActionTestParamsAttributesAction extends Action
{
    public function run(
        #[DescriptionAttribute('An int')] int $int,
        #[DescriptionAttribute('The name'),
        RegexAttribute('/^[a-z]$/')] string $name,
    ): array {
        return [];
    }
}
