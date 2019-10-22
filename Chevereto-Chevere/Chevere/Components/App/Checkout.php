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

use InvalidArgumentException;

use Chevere\Components\FileReturn\FileReturn;
use Chevere\Components\Message\Message;
use Chevere\Contracts\App\BuildContract;
use Chevere\Contracts\App\CheckoutContract;

/**
 * Checkout the application build
 */
final class Checkout implements CheckoutContract
{
    /** @var BuildContract */
    private $build;
    
    /** @var FileReturn */
    private $fileReturn;

    /**
     * {@inheritdoc}
     */
    public function __construct(BuildContract $build)
    {
        $this->build = $build;
        $this->assertIsBuilt();
        $this->fileReturn = new FileReturn($this->build->path());
        $this->fileReturn->put($this->build->checksums());
    }

    /**
     * {@inheritdoc}
     */
    public function checksum(): string
    {
        return $this->fileReturn->checksum();
    }

    /**
     * The BuildContract must be built to checkout the application.
     */
    private function assertIsBuilt(): void
    {
        if (!$this->build->isBuilt()) {
            throw new InvalidArgumentException(
                (new Message('Instance of %type% %argument% must be built to construct a %className% instance'))
                    ->code('%type%', BuildContract::class)
                    ->code('%argument%', '$build')
                    ->code('%className%', __CLASS__)
                    ->toString()
            );
        }
    }
}
