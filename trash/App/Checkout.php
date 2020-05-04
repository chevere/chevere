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

namespace Chevere\Components\App;

use Chevere\Components\App\Interfaces\BuildInterface;
use Chevere\Components\App\Interfaces\CheckoutInterface;
use Chevere\Components\Filesystem\FilePhp;
use Chevere\Components\Filesystem\FilePhpReturn;
use Chevere\Components\Filesystem\Interfaces\FilePhpReturnInterface;
use Chevere\Components\Message\Message;
use Chevere\Components\VarExportable\VarExportable;
use InvalidArgumentException;

/**
 * Checkout the application build.
 * The checkout consists in the creation of a build file which maps the build checksums.
 */
final class Checkout implements CheckoutInterface
{
    private BuildInterface $build;

    private FilePhpReturnInterface $phpFileReturn;

    public function __construct(BuildInterface $build)
    {
        $this->build = $build;
        $this->assertIsMaked();
        $file = $this->build->file();
        if ($file->exists()) {
            $file->remove();
        }
        $file->create();
        $this->phpFileReturn = new FilePhpReturn(
            new FilePhp($file)
        );
        $this->phpFileReturn->put(
            new VarExportable($this->build->checksums())
        );
    }

    public function fileReturn(): FilePhpReturnInterface
    {
        return $this->phpFileReturn;
    }

    public function checksum(): string
    {
        return $this->phpFileReturn->filePhp()->file()->checksum();
    }

    /**
     * The BuildInterface must be maked to checkout the application.
     */
    private function assertIsMaked(): void
    {
        if (!$this->build->isMaked()) {
            throw new InvalidArgumentException(
                (new Message('Instance of %type% %argument% must be built to construct a %className% instance'))
                    ->code('%type%', BuildInterface::class)
                    ->code('%argument%', '$build')
                    ->code('%className%', self::class)
                    ->toString()
            );
        }
    }
}
