<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\Runtime;

use Chevere\Components\Data\Data;
use Chevere\Components\Data\Traits\DataMethodTrait;
use Chevere\Components\Runtime\Contracts\SetContract;

/**
 * Runtime applies runtime config and provide data about the App Runtime.
 */
final class Runtime
{
    use DataMethodTrait;

    public function __construct(SetContract ...$runtimeContract)
    {
        $this->data = new Data([]);
        foreach ($runtimeContract as $runtimeSet) {
            $this->data = $this->data
                ->withAddedKey($runtimeSet->name(), $runtimeSet->value());
        }
        $this->data = $this->data
            ->withAddedKey('errorReportingLevel', error_reporting());
    }
}
