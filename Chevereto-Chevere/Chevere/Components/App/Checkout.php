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

namespace Chevere\Components\App;

use Chevere\Components\FileReturn\FileReturn;
use Chevere\Contracts\App\BuildContract;
use Chevere\Contracts\App\CheckoutContract;

final class Checkout implements CheckoutContract
{
    /** @var BuildContract */
    private $build;

    /** @var FileReturn */
    private $fileReturn;

    public function __construct(BuildContract $build)
    {
        $this->build = $build;
        $this->fileReturn = new FileReturn($this->build->path());
        $this->fileReturn->put($this->build->cacheChecksums());
    }

    public function build(): BuildContract
    {
        return $this->build;
    }

    public function checksum(): string
    {
        return $this->fileReturn->checksum();
    }
}
