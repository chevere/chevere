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

namespace Chevere\Runtime;

use Chevere\Data\Data;
use Chevere\Contracts\Runtime\RuntimeSetContract;
use Chevere\Data\Traits\DataKeyTrait;

/**
 * Runtime applies runtime config and provide data about the App Runtime.
 */
final class Runtime
{
    use DataKeyTrait;

    /** @var Data */
    private $data;

    public function __construct(RuntimeSetContract ...$runtimeContract)
    {
        $this->data = new Data();
        foreach ($runtimeContract as $k => $runtimeSet) {
            $runtimeSet->set();
            $this->data->setKey($runtimeSet::ID, $runtimeSet->value());
        }
        $this->data->setKey('errorReportingLevel', error_reporting());
        // $this->config = $config;
    }
}
